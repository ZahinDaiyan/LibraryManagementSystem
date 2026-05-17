<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
</head>

<body>

<h2>Login</h2>

<a href="RegisterView.php">Create New Account</a>

<br><br>

<form
    action="../Controllers/LoginController.php"
    method="POST"
    onsubmit="return validateLogin(this)"
    novalidate
>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email">
    <span id="emailErr"></span>

    <br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
    <span id="passwordErr"></span>

    <br><br>

    <button type="submit">Login</button>

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

<script src="js/auth.js"></script>

</body>
</html>
