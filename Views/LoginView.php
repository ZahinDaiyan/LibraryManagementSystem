<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>

<div class="auth-container">
    <h2>Login</h2>

    <a href="RegisterView.php">Create New Account</a>

    <form
        action="../Controllers/LoginController.php"
        method="POST"
        onsubmit="return validateLogin(this)"
        novalidate
    >

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">
        <span id="emailErr"></span>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <span id="passwordErr"></span>

        <button type="submit">Login</button>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>

</body>
</html>
