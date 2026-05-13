<?php

function getLibrarianBranchByUserId($conn, $userId)
{
    $sql = "SELECT u.branch_id, b.name AS branch_name, b.city AS branch_city, b.address AS branch_address
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $branch = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $branch;
}

function getBranchPolicyByBranchId($conn, $branchId)
{
    $sql = "SELECT * FROM branch_policies WHERE branch_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $policy = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $policy;
}

function getGenresList($conn)
{
    $sql = "SELECT id, name FROM genres ORDER BY name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $genres = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $genres;
}

function createGenre($conn, $name)
{
    $sql = "INSERT INTO genres (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $name);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function renameGenre($conn, $genreId, $name)
{
    $sql = "UPDATE genres SET name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $name, $genreId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function deleteGenreIfUnused($conn, $genreId)
{
    $checkSql = "SELECT COUNT(*) AS total_books FROM books WHERE genre_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, 'i', $genreId);
    mysqli_stmt_execute($checkStmt);

    $checkResult = mysqli_stmt_get_result($checkStmt);
    $row = mysqli_fetch_assoc($checkResult);
    mysqli_stmt_close($checkStmt);

    if ($row && (int)$row['total_books'] > 0) {
        return false;
    }

    $sql = "DELETE FROM genres WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $genreId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
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
            WHERE bi.branch_id = ?
            ORDER BY b.title ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function getBooksWithoutInventoryForBranch($conn, $branchId)
{
    $sql = "SELECT b.id, b.title, b.author
            FROM books b
            WHERE NOT EXISTS (
                SELECT 1 FROM branch_inventory bi
                WHERE bi.book_id = b.id AND bi.branch_id = ?
            )
            ORDER BY b.title ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function saveBranchInventory($conn, $branchId, $bookId, $totalCopies, $availableCopies)
{
    $checkSql = "SELECT id FROM branch_inventory WHERE branch_id = ? AND book_id = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, 'ii', $branchId, $bookId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    $existing = mysqli_fetch_assoc($checkResult);
    mysqli_stmt_close($checkStmt);

    if ($existing) {
        $sql = "UPDATE branch_inventory SET total_copies = ?, available_copies = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iii', $totalCopies, $availableCopies, $existing['id']);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    $sql = "INSERT INTO branch_inventory (book_id, branch_id, total_copies, available_copies) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiii', $bookId, $branchId, $totalCopies, $availableCopies);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
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
            WHERE br.branch_id = ? AND br.status = 'pending'
            ORDER BY br.id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function decideBorrowRequest($conn, $borrowRecordId, $branchId, $librarianId, $status)
{
    $sql = "UPDATE borrow_records
            SET status = ?, librarian_id = ?
            WHERE id = ? AND branch_id = ? AND status = 'pending'";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'siii', $status, $librarianId, $borrowRecordId, $branchId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($result && $status === 'active') {
        $inventorySql = "UPDATE branch_inventory bi
                         JOIN borrow_records br ON br.book_id = bi.book_id AND br.branch_id = bi.branch_id
                         SET bi.available_copies = CASE WHEN bi.available_copies > 0 THEN bi.available_copies - 1 ELSE 0 END
                         WHERE br.id = ? AND bi.branch_id = ?";
        $inventoryStmt = mysqli_prepare($conn, $inventorySql);
        mysqli_stmt_bind_param($inventoryStmt, 'ii', $borrowRecordId, $branchId);
        mysqli_stmt_execute($inventoryStmt);
        mysqli_stmt_close($inventoryStmt);
    }

    return $result;
}

function getBorrowRecordForReturnSearch($conn, $branchId, $query)
{
    $likeQuery = '%' . $query . '%';
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
            WHERE br.branch_id = ?
              AND br.status IN ('active', 'pending')
              AND (CAST(br.id AS CHAR) LIKE ? OR u.name LIKE ?)
            ORDER BY br.id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iss', $branchId, $likeQuery, $likeQuery);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function processBorrowReturn($conn, $borrowRecordId, $librarianId)
{
    $recordSql = "SELECT br.id, br.member_id, br.book_id, br.branch_id, br.due_date, br.status
                  FROM borrow_records br
                  WHERE br.id = ? LIMIT 1";
    $recordStmt = mysqli_prepare($conn, $recordSql);
    mysqli_stmt_bind_param($recordStmt, 'i', $borrowRecordId);
    mysqli_stmt_execute($recordStmt);
    $recordResult = mysqli_stmt_get_result($recordStmt);
    $record = mysqli_fetch_assoc($recordResult);
    mysqli_stmt_close($recordStmt);

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
                      librarian_id = ?
                  WHERE id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, 'ii', $librarianId, $borrowRecordId);
    $updateResult = mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    if (!$updateResult) {
        return array('success' => false, 'message' => 'Unable to update return');
    }

    $inventorySql = "UPDATE branch_inventory
                     SET available_copies = available_copies + 1
                     WHERE branch_id = ? AND book_id = ?";
    $inventoryStmt = mysqli_prepare($conn, $inventorySql);
    mysqli_stmt_bind_param($inventoryStmt, 'ii', $record['branch_id'], $record['book_id']);
    mysqli_stmt_execute($inventoryStmt);
    mysqli_stmt_close($inventoryStmt);

    $fineAmount = 0;
    if ($overdueDays > 0) {
        $fineAmount = $overdueDays * $fineRate;

        $fineSql = "INSERT INTO fines (borrow_record_id, member_id, branch_id, amount, reason, is_paid, paid_at)
                    VALUES (?, ?, ?, ?, ?, 0, NULL)";
        $reason = 'Overdue return';
        $fineStmt = mysqli_prepare($conn, $fineSql);
        mysqli_stmt_bind_param($fineStmt, 'iiids', $borrowRecordId, $record['member_id'], $record['branch_id'], $fineAmount, $reason);
        mysqli_stmt_execute($fineStmt);
        mysqli_stmt_close($fineStmt);
    }

    return array('success' => true, 'message' => 'Return processed', 'fine_amount' => $fineAmount, 'overdue_days' => $overdueDays, 'max_days' => $maxDays);
}

function issueManualFine($conn, $borrowRecordId, $memberId, $branchId, $amount, $reason)
{
    $sql = "INSERT INTO fines (borrow_record_id, member_id, branch_id, amount, reason, is_paid, paid_at)
            VALUES (?, ?, ?, ?, ?, 0, NULL)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiids', $borrowRecordId, $memberId, $branchId, $amount, $reason);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function markFineAsPaid($conn, $fineId)
{
    $sql = "UPDATE fines SET is_paid = 1, paid_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $fineId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
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
            WHERE br.branch_id = ? AND br.status = 'active'";

    if ($filter === 'overdue') {
        $sql .= " AND br.due_date < CURDATE()";
    } elseif ($filter === 'today') {
        $sql .= " AND br.due_date = CURDATE()";
    } elseif ($filter === 'week') {
        $sql .= " AND br.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    }

    $sql .= " ORDER BY br.due_date ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function searchMembersByBranch($conn, $branchId, $query)
{
    $likeQuery = '%' . $query . '%';
    $sql = "SELECT id, name, email, phone, branch_id
            FROM users
            WHERE role = 'member'
              AND branch_id = ?
              AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)
            ORDER BY name ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isss', $branchId, $likeQuery, $likeQuery, $likeQuery);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function getMemberBorrowHistory($conn, $memberId)
{
    $sql = "SELECT br.id, br.book_id, br.branch_id, br.status, br.borrow_date, br.due_date, br.return_date, b.title, b.author, br.renewals_count
            FROM borrow_records br
            JOIN books b ON b.id = br.book_id
            WHERE br.member_id = ?
            ORDER BY br.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $memberId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function getMemberFineHistory($conn, $memberId)
{
    $sql = "SELECT id, borrow_record_id, amount, reason, is_paid, paid_at, branch_id
            FROM fines
            WHERE member_id = ?
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $memberId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function getReservationWaitlistForBranch($conn, $branchId)
{
    $sql = "SELECT r.id, r.member_id, r.book_id, r.branch_id, r.reserved_at, r.status, u.name AS member_name, b.title AS book_title
            FROM reservations r
            JOIN users u ON u.id = r.member_id
            JOIN books b ON b.id = r.book_id
            WHERE r.branch_id = ? AND r.status = 'waiting'
            ORDER BY r.reserved_at ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

function fulfillReservation($conn, $reservationId, $branchId)
{
    $sql = "UPDATE reservations SET status = 'fulfilled' WHERE id = ? AND branch_id = ? AND status = 'waiting'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $reservationId, $branchId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function getBranchCatalogStats($conn, $branchId)
{
    $stats = array();

    $sql1 = "SELECT b.id, b.title, COUNT(br.id) AS borrow_total
             FROM books b
             JOIN borrow_records br ON br.book_id = b.id
             WHERE br.branch_id = ?
             GROUP BY b.id, b.title
             ORDER BY borrow_total DESC
             LIMIT 10";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, 'i', $branchId);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);
    $stats['most_borrowed'] = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $stats['most_borrowed'][] = $row;
    }
    mysqli_stmt_close($stmt1);

    $sql2 = "SELECT b.id, b.title
             FROM books b
             JOIN branch_inventory bi ON bi.book_id = b.id
             WHERE bi.branch_id = ?
               AND NOT EXISTS (
                   SELECT 1 FROM borrow_records br
                   WHERE br.book_id = b.id AND br.branch_id = ?
               )
             ORDER BY b.title ASC";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, 'ii', $branchId, $branchId);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    $stats['never_borrowed'] = array();
    while ($row = mysqli_fetch_assoc($result2)) {
        $stats['never_borrowed'][] = $row;
    }
    mysqli_stmt_close($stmt2);

    $sql3 = "SELECT g.name AS genre_name, COUNT(br.id) AS total_borrows
             FROM borrow_records br
             JOIN books b ON b.id = br.book_id
             LEFT JOIN genres g ON g.id = b.genre_id
             WHERE br.branch_id = ?
             GROUP BY g.name
             ORDER BY total_borrows DESC";
    $stmt3 = mysqli_prepare($conn, $sql3);
    mysqli_stmt_bind_param($stmt3, 'i', $branchId);
    mysqli_stmt_execute($stmt3);
    $result3 = mysqli_stmt_get_result($stmt3);
    $stats['borrows_by_genre'] = array();
    while ($row = mysqli_fetch_assoc($result3)) {
        $stats['borrows_by_genre'][] = $row;
    }
    mysqli_stmt_close($stmt3);

    return $stats;
}

function createAnnouncement($conn, $branchId, $authorId, $title, $body)
{
    $sql = "INSERT INTO announcements (branch_id, author_id, title, body, published_at)
            VALUES (NULLIF(?, 0), ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiss', $branchId, $authorId, $title, $body);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function getAnnouncementsForBranch($conn, $branchId)
{
    $sql = "SELECT a.id, a.branch_id, a.author_id, a.title, a.body, a.published_at, u.name AS author_name
            FROM announcements a
            JOIN users u ON u.id = a.author_id
            WHERE a.branch_id IS NULL OR a.branch_id = ?
            ORDER BY a.published_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

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
            WHERE r.from_branch_id = ? OR r.to_branch_id = ?
            ORDER BY r.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $branchId, $branchId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $rows;
}

function updateInterBranchRequestStatus($conn, $requestId, $status)
{
    $sql = "UPDATE inter_branch_requests SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $status, $requestId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

?>