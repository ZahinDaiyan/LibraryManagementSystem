<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register</title>
</head>

<body>

<h2>Member Registration</h2>

<a href="LoginView.php">Already Have Account? Login</a>

<br><br>

<form
    action="../Controllers/RegisterController.php"
    method="POST"
    onsubmit="return validateRegister(this)"
    novalidate
>

    <label for="name">Name:</label>
    <input type="text" name="name" id="name">
    <span id="nameErr"></span>

    <br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email">
    <span id="emailErr"></span>

    <br><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone">
    <span id="phoneErr"></span>

    <br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
    <span id="passwordErr"></span>

    <br><br>

    <label for="confirmPassword">Confirm Password:</label>
    <input type="password" name="confirmPassword" id="confirmPassword">
    <span id="confirmPasswordErr"></span>

    <br><br>

    <label for="branch_id">Primary Branch ID:</label>
    <input type="number" name="branch_id" id="branch_id">
    <span id="branchErr"></span>

    <br><br>

    <button type="submit">Register</button>

</form>

<p id="error">

<?php
echo isset($_SESSION['error']) ? $_SESSION['error'] : "";
?>

</p>

<p id="msg">

<?php
echo isset($_SESSION['msg']) ? $_SESSION['msg'] : "";
?>

</p>

<script src="../js/auth.js"></script>

</body>
</html>
