<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('librarianOperationsActionRespond')) {
    function librarianOperationsActionRespond($expectsJson, $success, $message, $extra = array(), $statusCode = 200)
    {
        $_SESSION['msg'] = $message;

        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array_merge(array(
                'success' => (bool)$success,
                'message' => $message
            ), $extra));
            exit();
        }

        $_SESSION['msg'] = $message;
        header('Location: ../Controllers/LibrarianOperationsController.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    librarianOperationsActionRespond($expectsJson, false, 'Invalid request method.', array(), 405);
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    librarianOperationsActionRespond($expectsJson, false, 'Unauthorized - please login as librarian.', array(), 403);
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;
$message = '';
$success = false;
$payload = array();


if ($action === 'create_genre') {
    $name = htmlspecialchars($_POST['name']);
    $success = createGenre($conn, $name) ? true : false;
    $message = $success ? 'Genre created' : 'Genre create failed';
} elseif ($action === 'rename_genre') {
    $genreId = (int)$_POST['genre_id'];
    $name = htmlspecialchars($_POST['name']);
    $success = renameGenre($conn, $genreId, $name) ? true : false;
    $message = $success ? 'Genre renamed' : 'Genre rename failed';
} elseif ($action === 'delete_genre') {
    $genreId = (int)$_POST['genre_id'];
    $result = deleteGenreIfUnused($conn, $genreId);
    $success = $result ? true : false;
    $message = $result ? 'Genre deleted' : 'Genre cannot be deleted because books are assigned';
} elseif ($action === 'save_inventory') {
    $bookId = (int)$_POST['book_id'];
    $totalCopies = (int)$_POST['total_copies'];
    $availableCopies = (int)$_POST['available_copies'];
    $success = saveBranchInventory($conn, $branchId, $bookId, $totalCopies, $availableCopies) ? true : false;
    $message = $success ? 'Inventory saved' : 'Inventory save failed';
} elseif ($action === 'decision_request') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $decision = $_POST['decision'];
    $status = $decision === 'approve' ? 'active' : 'rejected';
    $success = decideBorrowRequest($conn, $borrowRecordId, $branchId, $_SESSION['id'], $status) ? true : false;
    $message = $success ? 'Request updated' : 'Request update failed';
} elseif ($action === 'process_return') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $result = processBorrowReturn($conn, $borrowRecordId, $_SESSION['id']);
    $success = isset($result['success']) && $result['success'] ? true : false;
    $payload['fine_amount'] = isset($result['fine_amount']) ? (float)$result['fine_amount'] : 0;
    $payload['overdue_days'] = isset($result['overdue_days']) ? (int)$result['overdue_days'] : 0;
    $message = $result['success'] ? 'Return processed. Fine: ' . number_format((float)$result['fine_amount'], 2) : $result['message'];
} elseif ($action === 'issue_fine') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $memberId = (int)$_POST['member_id'];
    $amount = (float)$_POST['amount'];
    $reason = htmlspecialchars($_POST['reason']);

    $brRow = getBorrowRecordBranchMember($conn, $borrowRecordId);

    if (!$brRow) {
        $success = false;
        $message = 'Borrow record not found';
    } elseif ((int)$brRow['branch_id'] !== (int)$branchId) {
        $success = false;
        $message = 'Cannot issue fine: borrow record is not for your branch';
    } elseif ((int)$brRow['member_id'] !== (int)$memberId) {
        $success = false;
        $message = 'Member ID does not match the borrow record';
    } elseif ($amount <= 0) {
        $success = false;
        $message = 'Invalid fine amount';
    } else {
        $success = issueManualFine($conn, $borrowRecordId, $memberId, $branchId, $amount, $reason) ? true : false;
        $message = $success ? 'Fine issued' : 'Fine issue failed';
    }
} elseif ($action === 'pay_fine') {
    $fineId = (int)$_POST['fine_id'];
    $success = markFineAsPaid($conn, $fineId) ? true : false;
    $message = $success ? 'Fine marked as paid' : 'Unable to mark fine paid';
} elseif ($action === 'fulfill_reservation') {
    $reservationId = (int)$_POST['reservation_id'];
    $success = fulfillReservation($conn, $reservationId, $branchId) ? true : false;
    $message = $success ? 'Reservation fulfilled' : 'Reservation fulfilment failed';
} elseif ($action === 'create_announcement') {
    $targetBranch = isset($_POST['branch_id']) && $_POST['branch_id'] !== '' ? (int)$_POST['branch_id'] : 0;
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    $success = createAnnouncement($conn, $targetBranch, $_SESSION['id'], $title, $body) ? true : false;
    $message = $success ? 'Announcement posted' : 'Announcement post failed';
} elseif ($action === 'update_transfer') {
    $requestId = isset($_POST['request_id']) ? $_POST['request_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $res = updateInterBranchRequestStatus($conn, $requestId, $status);
    if (is_array($res)) {
        $success = isset($res['success']) && $res['success'] ? true : false;
        $payload['affected'] = isset($res['affected']) ? (int)$res['affected'] : 0;
        if ($success) {
            if (isset($res['affected']) && $res['affected'] > 0) {
                $message = 'Transfer updated';
            } else {
                $message = 'No transfer record updated (maybe status already set)';
            }
        } else {
            $message = 'Transfer update failed: ' . $res['error'];
        }
    } else {
        $success = false;
        $message = 'Transfer update failed: unknown error';
    }
}

Close($conn);

if ($message === '' && $action !== '') {
    $message = 'Unknown action';
}

librarianOperationsActionRespond($expectsJson, $success, $message, $payload, $success ? 200 : 400);

?>