<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../LoginView.php');
    exit();
}

$data = isset($_SESSION['librarian_ops']) ? $_SESSION['librarian_ops'] : array();
$branch = isset($data['branch']) ? $data['branch'] : array();
$genres = isset($data['genres']) ? $data['genres'] : array();
$inventory = isset($data['inventory']) ? $data['inventory'] : array();
$unassignedBooks = isset($data['unassigned_books']) ? $data['unassigned_books'] : array();
$pendingRequests = isset($data['pending_requests']) ? $data['pending_requests'] : array();
$activeLoans = isset($data['active_loans']) ? $data['active_loans'] : array();
$returnMatches = isset($data['return_matches']) ? $data['return_matches'] : array();
$reservations = isset($data['reservations']) ? $data['reservations'] : array();
$members = isset($data['members']) ? $data['members'] : array();
$memberHistory = isset($data['member_history']) ? $data['member_history'] : array();
$memberFines = isset($data['member_fines']) ? $data['member_fines'] : array();
$stats = isset($data['stats']) ? $data['stats'] : array('most_borrowed' => array(), 'never_borrowed' => array(), 'borrows_by_genre' => array());
$announcements = isset($data['announcements']) ? $data['announcements'] : array();
$transfers = isset($data['transfers']) ? $data['transfers'] : array();
$unpaidFines = isset($data['unpaid_fines']) ? $data['unpaid_fines'] : array();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Librarian Operations</title>
    <link rel="stylesheet" href="/LibraryManagementSystem/Views/css/librarian.css">
</head>
<body>

<h2>Librarian Operations</h2>
<p>Branch: <?php echo isset($branch['branch_name']) ? $branch['branch_name'] : 'Unassigned'; ?></p>
<a href="/LibraryManagementSystem/Controllers/LibrarianDashboardController.php">Back to Dashboard</a>

<?php if (isset($_SESSION['error']) && $_SESSION['error'] != '') { ?><p><?php echo $_SESSION['error']; ?></p><?php } ?>
<?php if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') { ?><p><?php echo $_SESSION['msg']; ?></p><?php } ?>

<hr>
<h3>Genres</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" onsubmit="return validateGenreForm(this)">
    <input type="hidden" name="action" value="create_genre">
    <input type="text" name="name" placeholder="New genre name">
    <button type="submit">Add Genre</button>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Name</th><th>Rename</th><th>Delete</th></tr>
    <?php foreach ($genres as $genre) { ?>
        <tr>
            <td><?php echo $genre['name']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" onsubmit="return validateGenreForm(this)">
                    <input type="hidden" name="action" value="rename_genre">
                    <input type="hidden" name="genre_id" value="<?php echo $genre['id']; ?>">
                    <input type="text" name="name" value="<?php echo $genre['name']; ?>">
                    <button type="submit">Save</button>
                </form>
            </td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">
                    <input type="hidden" name="action" value="delete_genre">
                    <input type="hidden" name="genre_id" value="<?php echo $genre['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Branch Inventory</h3>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Book</th><th>ISBN</th><th>Total</th><th>Available</th><th>Save</th></tr>
    <?php foreach ($inventory as $row) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['isbn']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">
                    <input type="hidden" name="action" value="save_inventory">
                    <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                    <input type="number" name="total_copies" value="<?php echo $row['total_copies']; ?>">
            </td>
            <td><input type="number" name="available_copies" value="<?php echo $row['available_copies']; ?>"></td>
            <td><button type="submit">Update</button></td>
                </form>
        </tr>
    <?php } ?>
</table>
<p><b>Books without branch inventory:</b></p>
<ul>
    <?php foreach ($unassignedBooks as $book) { ?>
        <li><?php echo $book['title']; ?> - <?php echo $book['author']; ?></li>
    <?php } ?>
</ul>

<hr>
<h3>Pending Borrow Requests</h3>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Record</th><th>Member</th><th>Book</th><th>Decision</th></tr>
    <?php foreach ($pendingRequests as $request) { ?>
        <tr>
            <td><?php echo $request['id']; ?></td>
            <td><?php echo $request['member_name']; ?></td>
            <td><?php echo $request['book_title']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="decision_request">
                    <input type="hidden" name="borrow_record_id" value="<?php echo $request['id']; ?>">
                    <input type="hidden" name="decision" value="approve">
                    <button type="submit">Approve</button>
                </form>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="decision_request">
                    <input type="hidden" name="borrow_record_id" value="<?php echo $request['id']; ?>">
                    <input type="hidden" name="decision" value="reject">
                    <button type="submit">Reject</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Process Returns</h3>
<form id="return_search_form" novalidate action="javascript:void(0);" method="POST">
    <input type="text" id="return_query" name="return_query" placeholder="Borrow record ID or member name">
    <button type="button" id="return_search_btn" onclick="searchReturnsAjax(document.getElementById('return_query').value)">Search</button>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Record</th><th>Member</th><th>Book</th><th>Status</th><th>Due</th><th>Action</th></tr>
    <tbody id="returnResultsBody">
    <?php foreach ($returnMatches as $record) { ?>
        <tr>
            <td><?php echo $record['id']; ?></td>
            <td><?php echo $record['member_name']; ?></td>
            <td><?php echo $record['book_title']; ?></td>
            <td><?php echo $record['status']; ?></td>
            <td><?php echo $record['due_date']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">
                    <input type="hidden" name="action" value="process_return">
                    <input type="hidden" name="borrow_record_id" value="<?php echo $record['id']; ?>">
                    <button type="submit">Mark Returned</button>
                </form>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<hr>
<h3>Issue Manual Fine</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" onsubmit="return validateFineForm(this)">
    <input type="hidden" name="action" value="issue_fine">
    <input type="number" name="borrow_record_id" placeholder="Borrow record ID">
    <input type="number" name="member_id" placeholder="Member ID">
    <input type="text" name="amount" placeholder="Amount">
    <input type="text" name="reason" placeholder="Reason">
    <button type="submit">Issue Fine</button>
</form>

<h3>Confirm Fine Payments</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">
    <input type="hidden" name="action" value="pay_fine">
    <input type="number" name="fine_id" placeholder="Fine ID">
    <button type="submit">Mark Paid</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Fine ID</th><th>Borrow ID</th><th>Member ID</th><th>Member</th><th>Book</th><th>Amount</th><th>Reason</th><th>Action</th></tr>
    <?php foreach ($unpaidFines as $fine) { ?>
        <tr>
            <td><?php echo $fine['id']; ?></td>
            <td><?php echo $fine['borrow_record_id']; ?></td>
            <td><?php echo $fine['member_id']; ?></td>
            <td><?php echo $fine['member_name']; ?></td>
            <td><?php echo isset($fine['book_title']) ? $fine['book_title'] : ''; ?></td>
            <td><?php echo $fine['amount']; ?></td>
            <td><?php echo $fine['reason']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="pay_fine">
                    <input type="hidden" name="fine_id" value="<?php echo $fine['id']; ?>">
                    <button type="submit">Mark Paid</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Active Loans</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsController.php" method="POST">
    <select name="loan_filter">
        <option value="">All</option>
        <option value="overdue">Overdue</option>
        <option value="today">Due Today</option>
        <option value="week">Due This Week</option>
    </select>
    <button type="submit">Filter</button>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>User ID</th><th>Borrow ID</th><th>Member</th><th>Book</th><th>Borrow</th><th>Due date</th></tr>
    <?php foreach ($activeLoans as $loan) { ?>
        <tr>
            <td><?php echo $loan['member_id']; ?></td>
            <td><?php echo $loan['id']; ?></td>
            <td><?php echo $loan['member_name']; ?></td>
            <td><?php echo $loan['book_title']; ?></td>
            <td><?php echo $loan['borrow_date']; ?></td>
            <td><?php echo $loan['due_date']; ?></td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Search Members</h3>
<form id="member_search_form" novalidate action="javascript:void(0);" method="POST">
    <input type="text" id="member_query" name="member_query" placeholder="Name, email, or phone" autocomplete="off" onkeyup="showHint(this.value); searchMembersAjax(this.value);">
    <button type="button" id="member_search_btn" onclick="searchMembersAjax(document.getElementById('member_query').value)">Search</button>
    <div id="txtHint" style="margin-top:8px;"></div>
</form>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Member</th><th>Email</th><th>Phone</th><th>History</th></tr>
    <tbody id="memberResultsBody">
    <?php foreach ($members as $member) { ?>
        <tr>
            <td><?php echo $member['name']; ?></td>
            <td><?php echo $member['email']; ?></td>
            <td><?php echo $member['phone']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsController.php" method="POST" style="display:inline;">
                    <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                    <button type="submit">View History</button>
                </form>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Loan History</th><th>Book</th><th>Status</th><th>Borrow</th><th>Due</th><th>Returned</th></tr>
    <?php foreach ($memberHistory as $history) { ?>
        <tr>
            <td><?php echo $history['id']; ?></td>
            <td><?php echo $history['title']; ?></td>
            <td><?php echo $history['status']; ?></td>
            <td><?php echo $history['borrow_date']; ?></td>
            <td><?php echo $history['due_date']; ?></td>
            <td><?php echo $history['return_date']; ?></td>
        </tr>
    <?php } ?>
</table>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Fine History</th><th>Amount</th><th>Reason</th><th>Paid</th></tr>
    <?php foreach ($memberFines as $fine) { ?>
        <tr>
            <td><?php echo $fine['id']; ?></td>
            <td><?php echo $fine['amount']; ?></td>
            <td><?php echo $fine['reason']; ?></td>
            <td><?php echo $fine['is_paid']; ?></td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Reservations</h3>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Reservation</th><th>Member</th><th>Book</th><th>Action</th></tr>
    <?php foreach ($reservations as $reservation) { ?>
        <tr>
            <td><?php echo $reservation['id']; ?></td>
            <td><?php echo $reservation['member_name']; ?></td>
            <td><?php echo $reservation['book_title']; ?></td>
            <td>
                <form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST">
                    <input type="hidden" name="action" value="fulfill_reservation">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit">Fulfil</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<hr>
<h3>Catalog Statistics</h3>
<p class="stats-intro">These summaries show branch activity: which titles are borrowed most, which titles have never been borrowed, and how borrowing is distributed by genre.</p>
<div class="stats-grid">
    <section class="stats-card">
        <h4>Most Borrowed Books</h4>
        <table class="stats-table">
            <thead>
                <tr><th>Book</th><th>Borrows</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($stats['most_borrowed'])) { ?>
                <?php foreach ($stats['most_borrowed'] as $item) { ?>
                    <tr>
                        <td><?php echo $item['title']; ?></td>
                        <td><?php echo $item['borrow_total']; ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="2">No borrow data available.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="stats-card">
        <h4>Never Borrowed</h4>
        <table class="stats-table">
            <thead>
                <tr><th>Title</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($stats['never_borrowed'])) { ?>
                <?php foreach ($stats['never_borrowed'] as $item) { ?>
                    <tr><td><?php echo $item['title']; ?></td></tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td>No titles in this category.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="stats-card">
        <h4>Total Borrows by Genre</h4>
        <table class="stats-table">
            <thead>
                <tr><th>Genre</th><th>Total borrows</th></tr>
            </thead>
            <tbody>
            <?php if (!empty($stats['borrows_by_genre'])) { ?>
                <?php foreach ($stats['borrows_by_genre'] as $item) { ?>
                    <tr>
                        <td><?php echo $item['genre_name']; ?></td>
                        <td><?php echo $item['total_borrows']; ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="2">No genre totals available.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </section>
</div>

<hr>
<h3>Announcements</h3>
<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianOperationsActionController.php" method="POST" onsubmit="return validateAnnouncementForm(this)">
    <input type="hidden" name="action" value="create_announcement">
    <input type="number" name="branch_id" placeholder="Branch ID or leave blank">
    <input type="text" name="title" placeholder="Title">
    <input type="text" name="body" placeholder="Body">
    <button type="submit">Post</button>
</form>
<ul>
    <?php foreach ($announcements as $announcement) { ?>
        <li><?php echo $announcement['title']; ?> - <?php echo $announcement['body']; ?></li>
    <?php } ?>
</ul>

<script src="/LibraryManagementSystem/Views/js/librarian_validation.js"></script>
<script>
function selectMemberSuggestion(name) {
    document.getElementById('member_query').value = name;
    document.getElementById('txtHint').innerHTML = '';
    searchMembersAjax(name);
}
</script>
<script src="/LibraryManagementSystem/Views/js/librarian_search.js"></script>
</body>
</html>
