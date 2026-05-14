<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$conn = Connect();
$branchInfo = getLibrarianBranchByUserId($conn, $_SESSION['id']);
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;
$message = '';

if ($action === 'create_genre') {
    $name = htmlspecialchars($_POST['name']);
    $message = createGenre($conn, $name) ? 'Genre created' : 'Genre create failed';
} elseif ($action === 'rename_genre') {
    $genreId = (int)$_POST['genre_id'];
    $name = htmlspecialchars($_POST['name']);
    $message = renameGenre($conn, $genreId, $name) ? 'Genre renamed' : 'Genre rename failed';
} elseif ($action === 'delete_genre') {
    $genreId = (int)$_POST['genre_id'];
    $result = deleteGenreIfUnused($conn, $genreId);
    $message = $result ? 'Genre deleted' : 'Genre cannot be deleted because books are assigned';
} elseif ($action === 'save_inventory') {
    $bookId = (int)$_POST['book_id'];
    $totalCopies = (int)$_POST['total_copies'];
    $availableCopies = (int)$_POST['available_copies'];
    $message = saveBranchInventory($conn, $branchId, $bookId, $totalCopies, $availableCopies) ? 'Inventory saved' : 'Inventory save failed';
} elseif ($action === 'decision_request') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $decision = $_POST['decision'];
    $status = $decision === 'approve' ? 'active' : 'rejected';
    $message = decideBorrowRequest($conn, $borrowRecordId, $branchId, $_SESSION['id'], $status) ? 'Request updated' : 'Request update failed';
} elseif ($action === 'process_return') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $result = processBorrowReturn($conn, $borrowRecordId, $_SESSION['id']);
    $message = $result['success'] ? 'Return processed. Fine: ' . number_format((float)$result['fine_amount'], 2) : $result['message'];
} elseif ($action === 'issue_fine') {
    $borrowRecordId = (int)$_POST['borrow_record_id'];
    $memberId = (int)$_POST['member_id'];
    $amount = (float)$_POST['amount'];
    $reason = htmlspecialchars($_POST['reason']);
    $message = issueManualFine($conn, $borrowRecordId, $memberId, $branchId, $amount, $reason) ? 'Fine issued' : 'Fine issue failed';
} elseif ($action === 'pay_fine') {
    $fineId = (int)$_POST['fine_id'];
    $message = markFineAsPaid($conn, $fineId) ? 'Fine marked as paid' : 'Unable to mark fine paid';
} elseif ($action === 'fulfill_reservation') {
    $reservationId = (int)$_POST['reservation_id'];
    $message = fulfillReservation($conn, $reservationId, $branchId) ? 'Reservation fulfilled' : 'Reservation fulfilment failed';
} elseif ($action === 'create_announcement') {
    $targetBranch = isset($_POST['branch_id']) && $_POST['branch_id'] !== '' ? (int)$_POST['branch_id'] : 0;
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    $message = createAnnouncement($conn, $targetBranch, $_SESSION['id'], $title, $body) ? 'Announcement posted' : 'Announcement post failed';
} elseif ($action === 'update_transfer') {
    $requestId = isset($_POST['request_id']) ? $_POST['request_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $res = updateInterBranchRequestStatus($conn, $requestId, $status);
    if (is_array($res)) {
        if ($res['success']) {
            if (isset($res['affected']) && $res['affected'] > 0) {
                $message = 'Transfer updated';
            } else {
                $message = 'No transfer record updated (maybe status already set)';
            }
        } else {
            $message = 'Transfer update failed: ' . $res['error'];
        }
    } else {
        $message = 'Transfer update failed: unknown error';
    }
}

Close($conn);

$_SESSION['msg'] = $message;
header('Location: ../Controllers/LibrarianOperationsController.php');
exit();

?>