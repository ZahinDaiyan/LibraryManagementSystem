<?php

function bmEsc($conn, $value)
{
    return mysqli_real_escape_string($conn, trim((string)$value));
}

function bmFetchAll($result)
{
    $rows = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function bmFetchOne($result)
{
    $rows = bmFetchAll($result);
    return count($rows) > 0 ? $rows[0] : null;
}

function bmGetManagerProfile($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT id, name, email, phone, role, profile_pic, branch_id, is_active, created_at
            FROM users
            WHERE id = $managerId AND role = 'branch_manager'
            LIMIT 1";
    return bmFetchOne(mysqli_query($conn, $sql));
}

function bmGetManagedBranches($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                b.*,
                (SELECT COUNT(*) FROM users l WHERE l.branch_id = b.id AND l.role = 'librarian') AS librarian_count,
                (SELECT COUNT(*) FROM borrow_records br WHERE br.branch_id = b.id AND br.status = 'active') AS active_loans,
                (SELECT COUNT(*) FROM borrow_records br WHERE br.branch_id = b.id AND br.status = 'active' AND br.due_date < CURDATE()) AS overdue_loans,
                (SELECT COALESCE(SUM(f.amount), 0) FROM fines f WHERE f.branch_id = b.id AND f.is_paid = 0) AS outstanding_fines
            FROM branches b
            WHERE b.manager_id = $managerId
            ORDER BY b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetDashboardStats($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                (SELECT COUNT(*) FROM branches b WHERE b.manager_id = $managerId) AS total_branches,
                (SELECT COUNT(*) FROM users l JOIN branches b ON b.id = l.branch_id WHERE b.manager_id = $managerId AND l.role = 'librarian') AS total_librarians,
                (SELECT COUNT(*) FROM borrow_records br JOIN branches b ON b.id = br.branch_id WHERE b.manager_id = $managerId AND br.status = 'active') AS total_active_loans,
                (SELECT COUNT(*) FROM borrow_records br JOIN branches b ON b.id = br.branch_id WHERE b.manager_id = $managerId AND br.status = 'active' AND br.due_date < CURDATE()) AS total_overdue_loans,
                (SELECT COALESCE(SUM(f.amount), 0) FROM fines f JOIN branches b ON b.id = f.branch_id WHERE b.manager_id = $managerId AND f.is_paid = 0) AS total_outstanding_fines";
    return bmFetchOne(mysqli_query($conn, $sql));
}

function bmGetManagedBranchById($conn, $managerId, $branchId)
{
    $managerId = (int)$managerId;
    $branchId = (int)$branchId;
    $sql = "SELECT * FROM branches WHERE id = $branchId AND manager_id = $managerId LIMIT 1";
    return bmFetchOne(mysqli_query($conn, $sql));
}

function bmCreateBranch($conn, $managerId, $name, $address, $city, $phone)
{
    $managerId = (int)$managerId;
    $name = bmEsc($conn, $name);
    $address = bmEsc($conn, $address);
    $city = bmEsc($conn, $city);
    $phone = bmEsc($conn, $phone);

    $sql = "INSERT INTO branches (name, address, city, phone, manager_id, is_active, created_at)
            VALUES ('$name', '$address', '$city', '$phone', $managerId, 1, NOW())";
    return mysqli_query($conn, $sql);
}

function bmUpdateBranch($conn, $managerId, $branchId, $name, $address, $city, $phone)
{
    $managerId = (int)$managerId;
    $branchId = (int)$branchId;

    if (!bmGetManagedBranchById($conn, $managerId, $branchId)) {
        return false;
    }

    $name = bmEsc($conn, $name);
    $address = bmEsc($conn, $address);
    $city = bmEsc($conn, $city);
    $phone = bmEsc($conn, $phone);

    $sql = "UPDATE branches
            SET name = '$name',
                address = '$address',
                city = '$city',
                phone = '$phone'
            WHERE id = $branchId AND manager_id = $managerId";
    return mysqli_query($conn, $sql);
}

function bmToggleBranchStatus($conn, $managerId, $branchId)
{
    $managerId = (int)$managerId;
    $branchId = (int)$branchId;
    $sql = "UPDATE branches
            SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
            WHERE id = $branchId AND manager_id = $managerId";
    $ok = mysqli_query($conn, $sql);
    return $ok && mysqli_affected_rows($conn) > 0;
}

function bmGetAssignableLibrarians($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.branch_id, b.name AS branch_name
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.role = 'librarian'
              AND u.is_active = 1
              AND (
                    u.branch_id IS NULL
                    OR u.branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                  )
            ORDER BY u.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetManagedLibrarians($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.branch_id, b.name AS branch_name, b.city AS branch_city
            FROM users u
            JOIN branches b ON b.id = u.branch_id
            WHERE u.role = 'librarian' AND b.manager_id = $managerId
            ORDER BY b.name ASC, u.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmAssignLibrarianToBranch($conn, $managerId, $librarianId, $branchId)
{
    $managerId = (int)$managerId;
    $librarianId = (int)$librarianId;
    $branchId = (int)$branchId;

    $sql = "UPDATE users u
            SET u.branch_id = $branchId
            WHERE u.id = $librarianId
              AND u.role = 'librarian'
              AND u.is_active = 1
              AND EXISTS (SELECT 1 FROM branches b WHERE b.id = $branchId AND b.manager_id = $managerId)
              AND (
                    u.branch_id IS NULL
                    OR u.branch_id IN (SELECT bm.id FROM branches bm WHERE bm.manager_id = $managerId)
                  )";
    $ok = mysqli_query($conn, $sql);
    return $ok && mysqli_affected_rows($conn) > 0;
}

function bmRemoveLibrarianAssignment($conn, $managerId, $librarianId)
{
    $managerId = (int)$managerId;
    $librarianId = (int)$librarianId;

    $sql = "UPDATE users u
            SET u.branch_id = NULL
            WHERE u.id = $librarianId
              AND u.role = 'librarian'
              AND u.branch_id IN (SELECT b.id FROM branches b WHERE b.manager_id = $managerId)";
    $ok = mysqli_query($conn, $sql);
    return $ok && mysqli_affected_rows($conn) > 0;
}

function bmGetBranchPolicies($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                b.id AS branch_id,
                b.name AS branch_name,
                b.city AS branch_city,
                bp.id AS policy_id,
                bp.max_borrow_days,
                bp.max_books_per_member,
                bp.fine_rate_per_day,
                bp.max_renewals
            FROM branches b
            LEFT JOIN branch_policies bp ON bp.branch_id = b.id
            WHERE b.manager_id = $managerId
            ORDER BY b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmSaveBranchPolicy($conn, $managerId, $branchId, $maxBorrowDays, $maxBooks, $fineRate, $maxRenewals)
{
    $managerId = (int)$managerId;
    $branchId = (int)$branchId;
    $maxBorrowDays = (int)$maxBorrowDays;
    $maxBooks = (int)$maxBooks;
    $fineRate = (float)$fineRate;
    $maxRenewals = (int)$maxRenewals;

    if (!bmGetManagedBranchById($conn, $managerId, $branchId)) {
        return false;
    }

    $existingSql = "SELECT id FROM branch_policies WHERE branch_id = $branchId LIMIT 1";
    $existing = bmFetchOne(mysqli_query($conn, $existingSql));

    if ($existing) {
        $sql = "UPDATE branch_policies
                SET max_borrow_days = $maxBorrowDays,
                    max_books_per_member = $maxBooks,
                    fine_rate_per_day = $fineRate,
                    max_renewals = $maxRenewals
                WHERE branch_id = $branchId";
    } else {
        $sql = "INSERT INTO branch_policies (branch_id, max_borrow_days, max_books_per_member, fine_rate_per_day, max_renewals)
                VALUES ($branchId, $maxBorrowDays, $maxBooks, $fineRate, $maxRenewals)";
    }

    return mysqli_query($conn, $sql);
}

function bmGetInventoryReport($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                b.name AS branch_name,
                b.city AS branch_city,
                bk.title,
                bk.author,
                bk.isbn,
                bi.total_copies,
                bi.available_copies
            FROM branch_inventory bi
            JOIN branches b ON b.id = bi.branch_id
            JOIN books bk ON bk.id = bi.book_id
            WHERE b.manager_id = $managerId
            ORDER BY b.name ASC, bk.title ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetBranchBorrowingStats($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                b.id AS branch_id,
                b.name AS branch_name,
                (SELECT COUNT(*) FROM borrow_records br WHERE br.branch_id = b.id AND br.status = 'active') AS active_loans,
                (SELECT COUNT(*) FROM borrow_records br WHERE br.branch_id = b.id AND br.status = 'active' AND br.due_date < CURDATE()) AS overdue_loans,
                (SELECT COALESCE(SUM(f.amount), 0) FROM fines f WHERE f.branch_id = b.id AND f.is_paid = 0) AS outstanding_fines
            FROM branches b
            WHERE b.manager_id = $managerId
            ORDER BY b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetMostBorrowedBooks($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                bk.id,
                bk.title,
                bk.author,
                COUNT(br.id) AS borrow_count
            FROM borrow_records br
            JOIN branches b ON b.id = br.branch_id
            JOIN books bk ON bk.id = br.book_id
            WHERE b.manager_id = $managerId
            GROUP BY bk.id, bk.title, bk.author
            ORDER BY borrow_count DESC, bk.title ASC
            LIMIT 10";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetTopBorrowingMembers($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                u.id,
                u.name,
                u.email,
                COUNT(br.id) AS borrow_count
            FROM borrow_records br
            JOIN branches b ON b.id = br.branch_id
            JOIN users u ON u.id = br.member_id
            WHERE b.manager_id = $managerId
            GROUP BY u.id, u.name, u.email
            ORDER BY borrow_count DESC, u.name ASC
            LIMIT 10";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetMembersWithOutstandingFines($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                u.id,
                u.name,
                u.email,
                b.name AS branch_name,
                SUM(f.amount) AS outstanding_amount,
                COUNT(f.id) AS fine_count
            FROM fines f
            JOIN branches b ON b.id = f.branch_id
            JOIN users u ON u.id = f.member_id
            WHERE b.manager_id = $managerId AND f.is_paid = 0
            GROUP BY u.id, u.name, u.email, b.name
            ORDER BY outstanding_amount DESC, u.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetNewMemberRegistrationsByBranch($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                b.name AS branch_name,
                COUNT(u.id) AS new_members
            FROM branches b
            LEFT JOIN users u ON u.branch_id = b.id AND u.role = 'member'
            WHERE b.manager_id = $managerId
            GROUP BY b.id, b.name
            ORDER BY b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetMonthlyReports($conn, $managerId, $branchId, $month)
{
    $managerId = (int)$managerId;
    $branchId = (int)$branchId;
    $month = bmEsc($conn, $month);
    $startDate = $month . "-01";
    $endDate = date('Y-m-t', strtotime($startDate));

    $sql = "SELECT
                b.id AS branch_id,
                b.name AS branch_name,
                (SELECT COUNT(*) FROM borrow_records br
                 WHERE br.branch_id = b.id AND br.borrow_date BETWEEN '$startDate' AND '$endDate') AS borrows,
                (SELECT COUNT(*) FROM borrow_records br
                 WHERE br.branch_id = b.id AND br.return_date BETWEEN '$startDate' AND '$endDate') AS returns_count,
                (SELECT COALESCE(SUM(f.amount), 0) FROM fines f
                 WHERE f.branch_id = b.id AND f.is_paid = 1 AND DATE(f.paid_at) BETWEEN '$startDate' AND '$endDate') AS fines_collected,
                (SELECT COUNT(*) FROM users u
                 WHERE u.branch_id = b.id AND u.role = 'member' AND DATE(u.created_at) BETWEEN '$startDate' AND '$endDate') AS new_members
            FROM branches b
            WHERE b.manager_id = $managerId
              AND ($branchId = 0 OR b.id = $branchId)
            ORDER BY b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetLibrarianActivity($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT
                u.id,
                u.name,
                u.email,
                b.name AS branch_name,
                (SELECT COUNT(*) FROM borrow_records br
                 WHERE br.librarian_id = u.id AND br.status = 'active') AS borrows_processed,
                (SELECT COUNT(*) FROM borrow_records br
                 WHERE br.librarian_id = u.id AND br.status = 'returned') AS returns_processed,
                (SELECT COUNT(*) FROM fines f
                 JOIN borrow_records br ON br.id = f.borrow_record_id
                 WHERE br.librarian_id = u.id) AS fines_issued
            FROM users u
            JOIN branches b ON b.id = u.branch_id
            WHERE u.role = 'librarian' AND b.manager_id = $managerId
            ORDER BY b.name ASC, u.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetOverdueAlerts($conn, $managerId, $thresholdDays)
{
    $managerId = (int)$managerId;
    $thresholdDays = (int)$thresholdDays;
    $sql = "SELECT
                br.id AS borrow_record_id,
                br.due_date,
                DATEDIFF(CURDATE(), br.due_date) AS overdue_days,
                u.name AS member_name,
                u.email AS member_email,
                bk.title AS book_title,
                b.name AS branch_name
            FROM borrow_records br
            JOIN branches b ON b.id = br.branch_id
            JOIN users u ON u.id = br.member_id
            JOIN books bk ON bk.id = br.book_id
            WHERE b.manager_id = $managerId
              AND br.status = 'active'
              AND br.due_date IS NOT NULL
              AND DATEDIFF(CURDATE(), br.due_date) > $thresholdDays
            ORDER BY overdue_days DESC, b.name ASC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmGetTransferRequests($conn, $managerId, $statusFilter)
{
    $managerId = (int)$managerId;
    $statusFilter = bmEsc($conn, $statusFilter);
    $statusSql = $statusFilter == '' ? '' : "AND r.status = '$statusFilter'";

    $sql = "SELECT
                r.id,
                r.book_id,
                r.from_branch_id,
                r.to_branch_id,
                r.requested_by,
                r.status,
                r.created_at,
                bk.title AS book_title,
                fb.name AS from_branch_name,
                tb.name AS to_branch_name,
                u.name AS requested_by_name
            FROM inter_branch_requests r
            JOIN books bk ON bk.id = r.book_id
            JOIN branches fb ON fb.id = r.from_branch_id
            JOIN branches tb ON tb.id = r.to_branch_id
            JOIN users u ON u.id = r.requested_by
            WHERE (
                    r.from_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                    OR r.to_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                  )
              $statusSql
            ORDER BY r.created_at DESC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

function bmUpdateTransferStatus($conn, $managerId, $requestId, $status)
{
    $managerId = (int)$managerId;
    $requestId = (int)$requestId;
    $status = bmEsc($conn, $status);

    if ($status === 'approved' || $status === 'rejected') {
        $sql = "UPDATE inter_branch_requests r
                SET r.status = '$status'
                WHERE r.id = $requestId
                  AND r.status = 'pending'
                  AND (
                        r.from_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                        OR r.to_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                      )";
        $ok = mysqli_query($conn, $sql);
        return $ok && mysqli_affected_rows($conn) > 0;
    }

    if ($status !== 'completed') {
        return false;
    }

    mysqli_begin_transaction($conn);

    $requestSql = "SELECT r.*
                   FROM inter_branch_requests r
                   WHERE r.id = $requestId
                     AND r.status = 'approved'
                     AND (
                           r.from_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                           OR r.to_branch_id IN (SELECT id FROM branches WHERE manager_id = $managerId)
                         )
                   LIMIT 1";
    $request = bmFetchOne(mysqli_query($conn, $requestSql));

    if (!$request) {
        mysqli_rollback($conn);
        return false;
    }

    $bookId = (int)$request['book_id'];
    $fromBranchId = (int)$request['from_branch_id'];
    $toBranchId = (int)$request['to_branch_id'];

    $sourceSql = "UPDATE branch_inventory
                  SET total_copies = total_copies - 1,
                      available_copies = available_copies - 1
                  WHERE book_id = $bookId
                    AND branch_id = $fromBranchId
                    AND total_copies > 0
                    AND available_copies > 0";
    $sourceOk = mysqli_query($conn, $sourceSql);

    if (!$sourceOk || mysqli_affected_rows($conn) <= 0) {
        mysqli_rollback($conn);
        return false;
    }

    $destCheckSql = "SELECT id FROM branch_inventory WHERE book_id = $bookId AND branch_id = $toBranchId LIMIT 1";
    $destRow = bmFetchOne(mysqli_query($conn, $destCheckSql));

    if ($destRow) {
        $destId = (int)$destRow['id'];
        $destSql = "UPDATE branch_inventory
                    SET total_copies = total_copies + 1,
                        available_copies = available_copies + 1
                    WHERE id = $destId";
    } else {
        $destSql = "INSERT INTO branch_inventory (book_id, branch_id, total_copies, available_copies)
                    VALUES ($bookId, $toBranchId, 1, 1)";
    }

    $destOk = mysqli_query($conn, $destSql);

    if (!$destOk) {
        mysqli_rollback($conn);
        return false;
    }

    $completeSql = "UPDATE inter_branch_requests SET status = 'completed' WHERE id = $requestId";
    $completeOk = mysqli_query($conn, $completeSql);

    if (!$completeOk) {
        mysqli_rollback($conn);
        return false;
    }

    mysqli_commit($conn);
    return true;
}

function bmCreatePlatformAnnouncement($conn, $managerId, $title, $body)
{
    $managerId = (int)$managerId;
    $title = bmEsc($conn, $title);
    $body = bmEsc($conn, $body);

    $sql = "INSERT INTO announcements (branch_id, author_id, title, body, published_at)
            VALUES (NULL, $managerId, '$title', '$body', NOW())";
    return mysqli_query($conn, $sql);
}

function bmGetManagerAnnouncements($conn, $managerId)
{
    $managerId = (int)$managerId;
    $sql = "SELECT a.*, u.name AS author_name
            FROM announcements a
            JOIN users u ON u.id = a.author_id
            WHERE a.author_id = $managerId OR a.branch_id IS NULL
            ORDER BY a.published_at DESC";
    return bmFetchAll(mysqli_query($conn, $sql));
}

?>
