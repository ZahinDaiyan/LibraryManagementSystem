<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
	header('Location: ../LoginView.php');
	exit();
}
// These three lines defensively extract data from a session array that might be incomplete or missing keys.
$librarian = isset($_SESSION['librarian']) ? $_SESSION['librarian'] : array();
$branchName = isset($librarian['branch_name']) && $librarian['branch_name'] != '' ? $librarian['branch_name'] : 'Unassigned';
$branchCity = isset($librarian['branch_city']) ? $librarian['branch_city'] : '';
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Librarian Dashboard</title>
    <link rel="stylesheet" href="../css/librarian.css">
</head>

<body class="librarian-dashboard-page">

<h2>Librarian Dashboard</h2>

<p>
	Welcome,
	<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>
</p>

<p>
	Assigned Branch:
	<?php echo $branchName; ?>
	<?php if ($branchCity != '') { echo ' - ' . $branchCity; } ?>
</p>

<?php if (isset($_SESSION['error']) && $_SESSION['error'] != '') { ?>
	<p><?php echo $_SESSION['error']; ?></p>
<?php } ?>

<?php if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') { ?>
	<p><?php echo $_SESSION['msg']; ?></p>
<?php } ?>

<hr>

<div class="tools-panel">
	<h3>Library Tools</h3>

	<ul>
		<li>
			<a href="/LibraryManagementSystem/Controllers/LibrarianProfileController.php">Manage Profile</a>
		</li>
		<li>
			<a href="/LibraryManagementSystem/Controllers/LibrarianBookCatalogController.php">Manage Book Catalog</a>
		</li>
		<li>
			<a href="/LibraryManagementSystem/Controllers/LibrarianOperationsController.php">Librarian Operations</a>
		</li>
		<li>
			<a href="/LibraryManagementSystem/Controllers/LibrarianBookFormController.php?mode=add">Add New Book</a>
		</li>
	</ul>

	<a class="logout-link" href="/LibraryManagementSystem/Controllers/LogoutController.php">
		<button>Logout</button>
	</a>
</div>

</body>
</html>