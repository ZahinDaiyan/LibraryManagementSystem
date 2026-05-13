<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../LoginView.php');
    exit();
}

$profile = isset($_SESSION['librarian_profile']) ? $_SESSION['librarian_profile'] : array();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Librarian Profile</title>
</head>

<body>

<h2>Librarian Profile</h2>

<a href="../../Controllers/LibrarianDashboardController.php">Back to Dashboard</a>

<?php if (isset($_SESSION['error']) && $_SESSION['error'] != '') { ?>
    <p><?php echo $_SESSION['error']; ?></p>
<?php } ?>

<?php if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') { ?>
    <p><?php echo $_SESSION['msg']; ?></p>
<?php } ?>

<hr>

<p><b>Branch:</b> <?php echo isset($profile['branch_name']) && $profile['branch_name'] != '' ? $profile['branch_name'] : 'Unassigned'; ?></p>
<p><b>City:</b> <?php echo isset($profile['branch_city']) ? $profile['branch_city'] : ''; ?></p>
<p><b>Address:</b> <?php echo isset($profile['branch_address']) ? $profile['branch_address'] : ''; ?></p>

<hr>

<form action="../../Controllers/LibrarianProfileUpdateController.php" method="POST">

    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?php echo isset($profile['name']) ? $profile['name'] : ''; ?>">

    <br><br>

    <label for="email">Email:</label>
    <input type="text" name="email" id="email" value="<?php echo isset($profile['email']) ? $profile['email'] : ''; ?>">

    <br><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" value="<?php echo isset($profile['phone']) ? $profile['phone'] : ''; ?>">

    <br><br>

    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" id="new_password">

    <br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" id="confirm_password">

    <br><br>

    <button type="submit">Update Profile</button>

</form>

</body>
</html>