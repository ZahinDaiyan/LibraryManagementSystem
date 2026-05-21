<?php

function ensureRenewalRequestsTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS renewal_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                borrow_record_id INT NOT NULL,
                member_id INT NOT NULL,
                branch_id INT NOT NULL,
                requested_extension_days INT NOT NULL DEFAULT 7,
                librarian_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                admin_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                manager_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                final_status VARCHAR(20) NOT NULL DEFAULT 'pending',
                requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                decided_at DATETIME NULL,
                INDEX idx_rr_borrow_record_id (borrow_record_id),
                INDEX idx_rr_branch_id (branch_id),
                INDEX idx_rr_member_id (member_id),
                INDEX idx_rr_final_status (final_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    return mysqli_query($conn, $sql) ? true : false;
}

function getPendingRenewalRequestsForLibrarian($conn, $librarianId)
{
    ensureRenewalRequestsTable($conn);

    $librarianId = mysqli_real_escape_string($conn, $librarianId);
    $branchSql = "SELECT branch_id FROM users WHERE id = '$librarianId' AND role = 'librarian' LIMIT 1";
    $branchRes = mysqli_query($conn, $branchSql);
    $branchRow = $branchRes ? mysqli_fetch_assoc($branchRes) : null;
    $branchId = $branchRow && isset($branchRow['branch_id']) ? (int)$branchRow['branch_id'] : 0;

    if ($branchId <= 0) {
        return array();
    }

    $branchIdEsc = mysqli_real_escape_string($conn, $branchId);
    $sql = "SELECT rr.*, br.id AS loan_id, br.due_date, br.renewals_count,
                   m.name AS member_name,
                   b.title AS book_title,
                   bn.name AS branch_name
            FROM renewal_requests rr
            JOIN borrow_records br ON br.id = rr.borrow_record_id
            JOIN users m ON m.id = rr.member_id
            JOIN books b ON b.id = br.book_id
            JOIN branches bn ON bn.id = rr.branch_id
            WHERE rr.final_status = 'pending'
              AND rr.librarian_status = 'pending'
              AND rr.branch_id = '$branchIdEsc'
            ORDER BY rr.requested_at ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function getPendingRenewalRequestsForManager($conn, $managerId)
{
    ensureRenewalRequestsTable($conn);

    $managerId = mysqli_real_escape_string($conn, $managerId);
    $sql = "SELECT rr.*, br.id AS loan_id, br.due_date, br.renewals_count,
                   m.name AS member_name,
                   b.title AS book_title,
                   bn.name AS branch_name
            FROM renewal_requests rr
            JOIN borrow_records br ON br.id = rr.borrow_record_id
            JOIN users m ON m.id = rr.member_id
            JOIN books b ON b.id = br.book_id
            JOIN branches bn ON bn.id = rr.branch_id
            WHERE rr.final_status = 'pending'
              AND rr.manager_status = 'pending'
              AND bn.manager_id = '$managerId'
            ORDER BY rr.requested_at ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function getPendingRenewalRequestsForAdmin($conn)
{
    ensureRenewalRequestsTable($conn);

    $sql = "SELECT rr.*, br.id AS loan_id, br.due_date, br.renewals_count,
                   m.name AS member_name,
                   b.title AS book_title,
                   bn.name AS branch_name
            FROM renewal_requests rr
            JOIN borrow_records br ON br.id = rr.borrow_record_id
            JOIN users m ON m.id = rr.member_id
            JOIN books b ON b.id = br.book_id
            JOIN branches bn ON bn.id = rr.branch_id
            WHERE rr.final_status = 'pending'
              AND rr.admin_status = 'pending'
            ORDER BY rr.requested_at ASC";

    $result = mysqli_query($conn, $sql);
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function createMemberNotification($conn, $memberId, $message)
{
    $memberId = mysqli_real_escape_string($conn, $memberId);
    $message = mysqli_real_escape_string($conn, $message);
    $sql = "INSERT INTO notifications (member_id, message, is_read, created_at)
            VALUES ('$memberId', '$message', 0, NOW())";
    return mysqli_query($conn, $sql);
}

function processRenewalApprovalDecision($conn, $requestId, $role, $actorId, $decision)
{
    ensureRenewalRequestsTable($conn);

    $requestIdEsc = mysqli_real_escape_string($conn, (int)$requestId);
    $actorIdEsc = mysqli_real_escape_string($conn, (int)$actorId);
    $decision = strtolower(trim($decision));
    if ($decision !== 'approved' && $decision !== 'rejected') {
        return array('success' => false, 'message' => 'Invalid decision');
    }

    $sql = "SELECT rr.*, br.id AS loan_id, br.status AS loan_status, br.member_id AS loan_member_id,
                   br.branch_id AS loan_branch_id, br.book_id, br.renewals_count
            FROM renewal_requests rr
            JOIN borrow_records br ON br.id = rr.borrow_record_id
            WHERE rr.id = '$requestIdEsc'
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    $request = $res ? mysqli_fetch_assoc($res) : null;

    if (!$request) {
        return array('success' => false, 'message' => 'Renewal request not found');
    }

    if (($request['final_status'] ?? '') !== 'pending') {
        return array('success' => false, 'message' => 'Renewal request already finalized');
    }

    if (($request['loan_status'] ?? '') !== 'active') {
        return array('success' => false, 'message' => 'Loan is no longer active');
    }

    $column = '';
    if ($role === 'librarian') {
        $branchSql = "SELECT branch_id FROM users WHERE id = '$actorIdEsc' AND role = 'librarian' LIMIT 1";
        $branchRes = mysqli_query($conn, $branchSql);
        $branchRow = $branchRes ? mysqli_fetch_assoc($branchRes) : null;
        $branchId = $branchRow && isset($branchRow['branch_id']) ? (int)$branchRow['branch_id'] : 0;

        if ($branchId <= 0 || $branchId !== (int)$request['loan_branch_id']) {
            return array('success' => false, 'message' => 'You are not assigned to this branch');
        }

        $column = 'librarian_status';
    } elseif ($role === 'branch_manager') {
        $mgrSql = "SELECT id FROM branches WHERE id = '" . mysqli_real_escape_string($conn, $request['loan_branch_id']) . "' AND manager_id = '$actorIdEsc' LIMIT 1";
        $mgrRes = mysqli_query($conn, $mgrSql);
        if (!$mgrRes || mysqli_num_rows($mgrRes) === 0) {
            return array('success' => false, 'message' => 'You do not manage this branch');
        }

        $column = 'manager_status';
    } elseif ($role === 'admin') {
        $column = 'admin_status';
    } else {
        return array('success' => false, 'message' => 'Unauthorized role');
    }

    if (($request[$column] ?? '') !== 'pending') {
        return array('success' => false, 'message' => 'You already reviewed this request');
    }

    $updateSql = "UPDATE renewal_requests
                  SET $column = '$decision'
                  WHERE id = '$requestIdEsc' AND final_status = 'pending'";
    if (!mysqli_query($conn, $updateSql)) {
        return array('success' => false, 'message' => 'Failed to save decision');
    }

    $reloadRes = mysqli_query($conn, "SELECT * FROM renewal_requests WHERE id = '$requestIdEsc' LIMIT 1");
    $rr = $reloadRes ? mysqli_fetch_assoc($reloadRes) : null;
    if (!$rr) {
        return array('success' => false, 'message' => 'Renewal request not found after update');
    }

    if ($decision === 'rejected') {
        mysqli_query($conn, "UPDATE renewal_requests SET final_status = 'rejected', decided_at = NOW() WHERE id = '$requestIdEsc'");

        $notifyMessage = 'Your loan renewal request was rejected by ' . str_replace('_', ' ', $role) . '.';
        createMemberNotification($conn, $request['loan_member_id'], $notifyMessage);

        return array('success' => true, 'message' => 'Renewal request rejected');
    }

    $allApproved = (($rr['librarian_status'] ?? '') === 'approved')
        && (($rr['manager_status'] ?? '') === 'approved')
        && (($rr['admin_status'] ?? '') === 'approved');

    if (!$allApproved) {
        return array('success' => true, 'message' => 'Decision saved. Waiting for other approvals.');
    }

    $loanIdEsc = mysqli_real_escape_string($conn, $request['loan_id']);
    $bookIdEsc = mysqli_real_escape_string($conn, $request['book_id']);
    $branchIdEsc = mysqli_real_escape_string($conn, $request['loan_branch_id']);

    $resSql = "SELECT id FROM reservations
               WHERE book_id = '$bookIdEsc'
                 AND branch_id = '$branchIdEsc'
                 AND status = 'waiting'
               LIMIT 1";
    $resRows = mysqli_query($conn, $resSql);
    if ($resRows && mysqli_num_rows($resRows) > 0) {
        mysqli_query($conn, "UPDATE renewal_requests SET final_status = 'rejected', decided_at = NOW() WHERE id = '$requestIdEsc'");
        createMemberNotification($conn, $request['loan_member_id'], 'Your loan renewal request was rejected because the book is reserved by another member.');
        return array('success' => true, 'message' => 'Renewal auto-rejected due to waitlist.');
    }

    $policySql = "SELECT max_renewals FROM branch_policies WHERE branch_id = '$branchIdEsc' LIMIT 1";
    $policyRes = mysqli_query($conn, $policySql);
    $policy = $policyRes ? mysqli_fetch_assoc($policyRes) : null;
    $maxRenewals = $policy && isset($policy['max_renewals']) ? (int)$policy['max_renewals'] : 1;
    $renewalsCount = isset($request['renewals_count']) ? (int)$request['renewals_count'] : 0;

    if ($renewalsCount >= $maxRenewals) {
        mysqli_query($conn, "UPDATE renewal_requests SET final_status = 'rejected', decided_at = NOW() WHERE id = '$requestIdEsc'");
        createMemberNotification($conn, $request['loan_member_id'], 'Your loan renewal request was rejected because max renewals have already been used.');
        return array('success' => true, 'message' => 'Renewal auto-rejected due to max renewals reached.');
    }

    $extensionDays = isset($rr['requested_extension_days']) ? (int)$rr['requested_extension_days'] : 7;
    if ($extensionDays <= 0) {
        $extensionDays = 7;
    }

    $applySql = "UPDATE borrow_records
                 SET due_date = DATE_ADD(due_date, INTERVAL $extensionDays DAY),
                     renewals_count = COALESCE(renewals_count, 0) + 1
                 WHERE id = '$loanIdEsc' AND status = 'active'";
    $applyOk = mysqli_query($conn, $applySql);

    if (!$applyOk) {
        return array('success' => false, 'message' => 'All approved, but failed to apply renewal');
    }

    mysqli_query($conn, "UPDATE renewal_requests SET final_status = 'approved', decided_at = NOW() WHERE id = '$requestIdEsc'");
    createMemberNotification($conn, $request['loan_member_id'], 'Your loan renewal request has been approved and applied.');

    return array('success' => true, 'message' => 'All approvals received. Renewal applied.');
}

function getActiveLoans($conn, $member_id)
{
    ensureRenewalRequestsTable($conn);

    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT br.*, b.title AS book_title, brn.name AS branch_name,
            rr.final_status AS renewal_request_status,
            rr.requested_at AS renewal_requested_at,
            DATEDIFF(br.due_date, CURDATE()) AS days_remaining
            FROM borrow_records br
            JOIN books b ON br.book_id = b.id
            JOIN branches brn ON br.branch_id = brn.id
            LEFT JOIN renewal_requests rr ON rr.borrow_record_id = br.id AND rr.final_status = 'pending'
            WHERE br.member_id = '$member_id' AND br.status = 'active'
            ORDER BY br.due_date ASC";
    
    $result = mysqli_query($conn, $sql);
    $loans = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $loans[] = $row;
    }
    return $loans;
}

function getBorrowHistory($conn, $member_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $sql = "SELECT br.*, b.title AS book_title, brn.name AS branch_name
            FROM borrow_records br
            JOIN books b ON br.book_id = b.id
            JOIN branches brn ON br.branch_id = brn.id
            WHERE br.member_id = '$member_id'
            ORDER BY br.borrow_date DESC";
    
    $result = mysqli_query($conn, $sql);
    $history = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }
    return $history;
}

function requestRenewal($conn, $loan_id, $member_id)
{
    ensureRenewalRequestsTable($conn);

    $loan_id = mysqli_real_escape_string($conn, $loan_id);
    $member_id = mysqli_real_escape_string($conn, $member_id);

    $loanSql = "SELECT id, member_id, book_id, branch_id, status, renewals_count
                FROM borrow_records
                WHERE id = '$loan_id' AND member_id = '$member_id'
                LIMIT 1";
    $loanRes = mysqli_query($conn, $loanSql);
    $loan = $loanRes ? mysqli_fetch_assoc($loanRes) : null;

    if (!$loan || ($loan['status'] ?? '') !== 'active') {
        return ['success' => false, 'message' => 'Loan is not active'];
    }

    $pendingSql = "SELECT id FROM renewal_requests
                   WHERE borrow_record_id = '" . mysqli_real_escape_string($conn, $loan['id']) . "'
                     AND final_status = 'pending'
                   LIMIT 1";
    $pendingRes = mysqli_query($conn, $pendingSql);
    if ($pendingRes && mysqli_num_rows($pendingRes) > 0) {
        return ['success' => false, 'message' => 'Renewal request already pending approval'];
    }

    $branchIdEsc = mysqli_real_escape_string($conn, $loan['branch_id']);
    $policySql = "SELECT max_renewals FROM branch_policies WHERE branch_id = '$branchIdEsc' LIMIT 1";
    $policyRes = mysqli_query($conn, $policySql);
    $policy = $policyRes ? mysqli_fetch_assoc($policyRes) : null;
    $maxRenewals = $policy && isset($policy['max_renewals']) ? (int)$policy['max_renewals'] : 1;
    $renewalsCount = isset($loan['renewals_count']) ? (int)$loan['renewals_count'] : 0;

    if ($renewalsCount >= $maxRenewals) {
        return ['success' => false, 'message' => 'Maximum renewals reached for this loan'];
    }
    
    $bookIdEsc = mysqli_real_escape_string($conn, $loan['book_id']);
    $sql_check = "SELECT id FROM reservations WHERE book_id = '$bookIdEsc' AND status = 'waiting' LIMIT 1";
    $res_check = mysqli_query($conn, $sql_check);
    
    if ($res_check && mysqli_num_rows($res_check) > 0) {
        return ['success' => false, 'message' => 'Book is reserved by another member'];
    }

    $insertSql = "INSERT INTO renewal_requests
                  (borrow_record_id, member_id, branch_id, requested_extension_days, librarian_status, admin_status, manager_status, final_status, requested_at)
                  VALUES
                  ('$loan_id', '$member_id', '$branchIdEsc', 7, 'pending', 'pending', 'pending', 'pending', NOW())";

    if (mysqli_query($conn, $insertSql)) {
        return ['success' => true, 'message' => 'Renewal request submitted. Waiting for librarian, branch manager, and admin approval.'];
    }

    return ['success' => false, 'message' => 'Failed to submit renewal request'];
}

function checkBookAvailabilityInBranch($conn, $book_id, $branch_id)
{
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $branch_id = mysqli_real_escape_string($conn, $branch_id);
    $sql = "SELECT available_copies 
            FROM branch_inventory 
            WHERE book_id='$book_id' AND branch_id='$branch_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return ($row && $row['available_copies'] > 0);
}

function hasPendingBorrowRequest($conn, $member_id, $book_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $sql = "SELECT id FROM borrow_records 
            WHERE member_id='$member_id' 
            AND book_id='$book_id' 
            AND status='pending'";
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

function createBorrowRequest($conn, $member_id, $book_id, $branch_id)
{
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $book_id = mysqli_real_escape_string($conn, $book_id);
    $branch_id = mysqli_real_escape_string($conn, $branch_id);
    $sql = "INSERT INTO borrow_records
            (member_id, book_id, branch_id, status, borrow_date)
            VALUES
            ('$member_id', '$book_id', '$branch_id', 'pending', CURDATE())";
    return mysqli_query($conn, $sql);
}

?>

