<?php

function getLibrarianBranchByUserId($conn, $userId)
{
    $sql = "SELECT u.branch_id, b.name AS branch_name, b.city AS branch_city, b.address AS branch_address
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = '$userId'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);
    $branch = mysqli_fetch_assoc($result);

    return $branch;
}

function getBranchPolicyByBranchId($conn, $branchId)
{
    $sql = "SELECT * FROM branch_policies WHERE branch_id = '$branchId' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $policy = mysqli_fetch_assoc($result);

    return $policy;
}

function getGenresList($conn)
{
    $sql = "SELECT id, name FROM genres ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $genres = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }

    return $genres;
}

function createGenre($conn, $name)
{
    $sql = "INSERT INTO genres (name) VALUES ('$name')";
    return mysqli_query($conn, $sql);
}

function renameGenre($conn, $genreId, $name)
{
    $sql = "UPDATE genres SET name = '$name' WHERE id = '$genreId'";
    return mysqli_query($conn, $sql);
}

function deleteGenreIfUnused($conn, $genreId)
{
    $checkSql = "SELECT COUNT(*) AS total_books FROM books WHERE genre_id = '$genreId'";
    $checkResult = mysqli_query($conn, $checkSql);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row && (int)$row['total_books'] > 0) {
        return false;
    }

    $sql = "DELETE FROM genres WHERE id = '$genreId'";
    return mysqli_query($conn, $sql);
}

function getBranchInventoryRows($conn, $branchId)
{
    $sql = "SELECT
                bi.id,
                bi.book_id,
                bi.branch_id,
                bi.total_copies,
                bi.available_copies,
                b.title,
                b.author,
                b.isbn
            FROM branch_inventory bi
            JOIN books b ON b.id = bi.book_id
            WHERE bi.branch_id = '$branchId'
            ORDER BY b.title ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getBooksWithoutInventoryForBranch($conn, $branchId)
{
    $sql = "SELECT b.id, b.title, b.author
            FROM books b
            WHERE NOT EXISTS (
                SELECT 1 FROM branch_inventory bi
                WHERE bi.book_id = b.id AND bi.branch_id = '$branchId'
            )
            ORDER BY b.title ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function saveBranchInventory($conn, $branchId, $bookId, $totalCopies, $availableCopies)
{
    $checkSql = "SELECT id FROM branch_inventory WHERE branch_id = '$branchId' AND book_id = '$bookId' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkSql);
    $existing = mysqli_fetch_assoc($checkResult);

    if ($existing) {
        $sql = "UPDATE branch_inventory SET total_copies = '$totalCopies', available_copies = '$availableCopies' WHERE id = '{$existing['id']}'";
        return mysqli_query($conn, $sql);
    }

    $sql = "INSERT INTO branch_inventory (book_id, branch_id, total_copies, available_copies) VALUES ('$bookId', '$branchId', '$totalCopies', '$availableCopies')";
    return mysqli_query($conn, $sql);
}

function getBranchInventoryRow($conn, $branchId, $bookId)
{
    $sql = "SELECT * FROM branch_inventory WHERE branch_id = '$branchId' AND book_id = '$bookId' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row : null;
}

function getPendingBorrowRequestsForBranch($conn, $branchId)
{
    $sql = "SELECT
                br.id,
                br.member_id,
                br.book_id,
                br.branch_id,
                br.borrow_date,
                u.name AS member_name,
                u.email AS member_email,
                b.title AS book_title,
                b.author AS book_author
            FROM borrow_records br
            JOIN users u ON u.id = br.member_id
            JOIN books b ON b.id = br.book_id
            WHERE br.branch_id = '$branchId' AND br.status = 'pending'
            ORDER BY br.id DESC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function decideBorrowRequest($conn, $borrowRecordId, $branchId, $librarianId, $status)
{
    $sql = "UPDATE borrow_records
            SET status = '$status', librarian_id = '$librarianId'
            WHERE id = '$borrowRecordId' AND branch_id = '$branchId' AND status = 'pending'";

    $result = mysqli_query($conn, $sql);

    if ($result && $status === 'active') {
        $inventorySql = "UPDATE branch_inventory bi
                         JOIN borrow_records br ON br.book_id = bi.book_id AND br.branch_id = bi.branch_id
                         SET bi.available_copies = CASE WHEN bi.available_copies > 0 THEN bi.available_copies - 1 ELSE 0 END
                         WHERE br.id = '$borrowRecordId' AND bi.branch_id = '$branchId'";
        mysqli_query($conn, $inventorySql);
    }

    return $result;
}

function getBorrowRecordForReturnSearch($conn, $branchId, $query)
{
    $likeQuery = '%' . mysqli_real_escape_string($conn, $query) . '%';
    $sql = "SELECT
                br.id,
                br.member_id,
                br.book_id,
                br.branch_id,
                br.status,
                br.borrow_date,
                br.due_date,
                br.return_date,
                u.name AS member_name,
                u.email AS member_email,
                b.title AS book_title
            FROM borrow_records br
            JOIN users u ON u.id = br.member_id
            JOIN books b ON b.id = br.book_id
            WHERE br.branch_id = '$branchId'
              AND br.status IN ('active', 'pending')
              AND (CAST(br.id AS CHAR) LIKE '$likeQuery' OR u.name LIKE '$likeQuery')
            ORDER BY br.id DESC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function processBorrowReturn($conn, $borrowRecordId, $librarianId)
{
    $recordSql = "SELECT br.id, br.member_id, br.book_id, br.branch_id, br.due_date, br.status
                  FROM borrow_records br
                  WHERE br.id = '$borrowRecordId' LIMIT 1";
    $recordResult = mysqli_query($conn, $recordSql);
    $record = mysqli_fetch_assoc($recordResult);

    if (!$record || $record['status'] !== 'active') {
        return array('success' => false, 'message' => 'Borrow record is not active');
    }

    $policy = getBranchPolicyByBranchId($conn, $record['branch_id']);
    $maxDays = $policy && isset($policy['max_borrow_days']) ? (int)$policy['max_borrow_days'] : 14;
    $fineRate = $policy && isset($policy['fine_rate_per_day']) ? (float)$policy['fine_rate_per_day'] : 0;

    $today = date('Y-m-d');
    $dueDate = $record['due_date'];
    $overdueDays = 0;

    if ($dueDate && $today > $dueDate) {
        $due = new DateTime($dueDate);
        $now = new DateTime($today);
        $interval = $due->diff($now);
        $overdueDays = (int)$interval->days;
    }

    $updateSql = "UPDATE borrow_records
                  SET status = 'returned',
                      return_date = CURDATE(),
                      librarian_id = '$librarianId'
                  WHERE id = '$borrowRecordId'";
    $updateResult = mysqli_query($conn, $updateSql);

    if (!$updateResult) {
        return array('success' => false, 'message' => 'Unable to update return');
    }

    $inventorySql = "UPDATE branch_inventory
                     SET available_copies = available_copies + 1
                     WHERE branch_id = '{$record['branch_id']}' AND book_id = '{$record['book_id']}'";
    mysqli_query($conn, $inventorySql);

    $fineAmount = 0;
    if ($overdueDays > 0) {
        $fineAmount = $overdueDays * $fineRate;
        $reason = 'Overdue return';
        $fineSql = "INSERT INTO fines (borrow_record_id, member_id, branch_id, amount, reason, is_paid, paid_at)
                    VALUES ('$borrowRecordId', '{$record['member_id']}', '{$record['branch_id']}', '$fineAmount', '$reason', 0, NULL)";
        mysqli_query($conn, $fineSql);
    }

    return array('success' => true, 'message' => 'Return processed', 'fine_amount' => $fineAmount, 'overdue_days' => $overdueDays, 'max_days' => $maxDays);
}

function issueManualFine($conn, $borrowRecordId, $memberId, $branchId, $amount, $reason)
{
    $sql = "INSERT INTO fines (borrow_record_id, member_id, branch_id, amount, reason, is_paid, paid_at)
            VALUES ('$borrowRecordId', '$memberId', '$branchId', '$amount', '$reason', 0, NULL)";
    return mysqli_query($conn, $sql);
}

function markFineAsPaid($conn, $fineId)
{
    $sql = "UPDATE fines SET is_paid = 1, paid_at = NOW() WHERE id = '$fineId'";
    return mysqli_query($conn, $sql);
}

function getActiveLoansForBranch($conn, $branchId, $filter)
{
    $sql = "SELECT
                br.id,
                br.member_id,
                br.book_id,
                br.branch_id,
                br.borrow_date,
                br.due_date,
                br.status,
                u.name AS member_name,
                b.title AS book_title
            FROM borrow_records br
            JOIN users u ON u.id = br.member_id
            JOIN books b ON b.id = br.book_id
            WHERE br.branch_id = '$branchId' AND br.status = 'active'";

    if ($filter === 'overdue') {
        $sql .= " AND br.due_date < CURDATE()";
    } elseif ($filter === 'today') {
        $sql .= " AND br.due_date = CURDATE()";
    } elseif ($filter === 'week') {
        $sql .= " AND br.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    }

    $sql .= " ORDER BY br.due_date ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function searchMembersByBranch($conn, $branchId, $query)
{
    $likeQuery = '%' . mysqli_real_escape_string($conn, $query) . '%';
    $sql = "SELECT id, name, email, phone, branch_id
            FROM users
            WHERE role = 'member'
              AND branch_id = '$branchId'
              AND (name LIKE '$likeQuery' OR email LIKE '$likeQuery' OR phone LIKE '$likeQuery')
            ORDER BY name ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getMemberBorrowHistory($conn, $memberId)
{
    $sql = "SELECT br.id, br.book_id, br.branch_id, br.status, br.borrow_date, br.due_date, br.return_date, b.title, b.author, br.renewals_count
            FROM borrow_records br
            JOIN books b ON b.id = br.book_id
            WHERE br.member_id = '$memberId'
            ORDER BY br.id DESC";
    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getMemberFineHistory($conn, $memberId)
{
    $sql = "SELECT id, borrow_record_id, amount, reason, is_paid, paid_at, branch_id
            FROM fines
            WHERE member_id = '$memberId'
            ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getReservationWaitlistForBranch($conn, $branchId)
{
    $sql = "SELECT r.id, r.member_id, r.book_id, r.branch_id, r.reserved_at, r.status, u.name AS member_name, b.title AS book_title
            FROM reservations r
            JOIN users u ON u.id = r.member_id
            JOIN books b ON b.id = r.book_id
            WHERE r.branch_id = '$branchId' AND r.status = 'waiting'
            ORDER BY r.reserved_at ASC";
    $result = mysqli_query($conn, $sql);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function fulfillReservation($conn, $reservationId, $branchId)
{
    $sql = "UPDATE reservations SET status = 'fulfilled' WHERE id = '$reservationId' AND branch_id = '$branchId' AND status = 'waiting'";
    return mysqli_query($conn, $sql);
}

function getBranchCatalogStats($conn, $branchId)
{
    $stats = array();

    $sql1 = "SELECT b.id, b.title, COUNT(br.id) AS borrow_total
             FROM books b
             JOIN borrow_records br ON br.book_id = b.id
             WHERE br.branch_id = '$branchId'
             GROUP BY b.id, b.title
             ORDER BY borrow_total DESC
             LIMIT 10";
    $result1 = mysqli_query($conn, $sql1);
    $stats['most_borrowed'] = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $stats['most_borrowed'][] = $row;
    }

    $sql2 = "SELECT b.id, b.title
             FROM books b
             JOIN branch_inventory bi ON bi.book_id = b.id
             WHERE bi.branch_id = '$branchId'
               AND NOT EXISTS (
                   SELECT 1 FROM borrow_records br
                   WHERE br.book_id = b.id AND br.branch_id = '$branchId'
               )
             ORDER BY b.title ASC";
    $result2 = mysqli_query($conn, $sql2);
    $stats['never_borrowed'] = array();
    while ($row = mysqli_fetch_assoc($result2)) {
        $stats['never_borrowed'][] = $row;
    }

    $sql3 = "SELECT g.name AS genre_name, COUNT(br.id) AS total_borrows
             FROM borrow_records br
             JOIN books b ON b.id = br.book_id
             LEFT JOIN genres g ON g.id = b.genre_id
             WHERE br.branch_id = '$branchId'
             GROUP BY g.name
             ORDER BY total_borrows DESC";
    $result3 = mysqli_query($conn, $sql3);
    $stats['borrows_by_genre'] = array();
    while ($row = mysqli_fetch_assoc($result3)) {
        $stats['borrows_by_genre'][] = $row;
    }

    return $stats;
}

function createAnnouncement($conn, $branchId, $authorId, $title, $body)
{
    $branch_val = $branchId == 0 ? "NULL" : "'$branchId'";
    $sql = "INSERT INTO announcements (branch_id, author_id, title, body, published_at)
            VALUES ($branch_val, '$authorId', '$title', '$body', NOW())";
    return mysqli_query($conn, $sql);
}

function getAnnouncementsForBranch($conn, $branchId)
{
    $sql = "SELECT a.id, a.branch_id, a.author_id, a.title, a.body, a.published_at, u.name AS author_name
            FROM announcements a
            JOIN users u ON u.id = a.author_id
            WHERE a.branch_id IS NULL OR a.branch_id = '$branchId'
            ORDER BY a.published_at DESC";
    $result = mysqli_query($conn, $sql);
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function getInterBranchRequestsForBranch($conn, $branchId)
{
    $sql = "SELECT r.id, r.book_id, r.from_branch_id, r.to_branch_id, r.requested_by, r.status, r.created_at,
                   b.title AS book_title,
                   fb.name AS from_branch_name,
                   tb.name AS to_branch_name,
                   u.name AS requested_by_name
            FROM inter_branch_requests r
            JOIN books b ON b.id = r.book_id
            JOIN branches fb ON fb.id = r.from_branch_id
            JOIN branches tb ON tb.id = r.to_branch_id
            JOIN users u ON u.id = r.requested_by
            WHERE r.from_branch_id = '$branchId' OR r.to_branch_id = '$branchId'
            ORDER BY r.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function updateInterBranchRequestStatus($conn, $requestId, $status)
{
    $requestIdEsc = intval($requestId);
    $statusEsc = mysqli_real_escape_string($conn, $status);

    $sql = "UPDATE inter_branch_requests SET status = '$statusEsc' WHERE id = '$requestIdEsc'";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        return array('success' => false, 'error' => mysqli_error($conn));
    }

    $affected = mysqli_affected_rows($conn);
    return array('success' => true, 'affected' => $affected);
}

?>