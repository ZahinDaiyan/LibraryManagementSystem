<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>

<div class="auth-container">
    <h2>Member Registration</h2>

    <a href="LoginView.php">Already Have Account? Login</a>

    <form
        action="../Controllers/RegisterController.php"
        method="POST"
        onsubmit="return validateRegister(this)"
        novalidate
    >

        <label for="name">Name:</label>
        <input type="text" name="name" id="name">
        <span id="nameErr"></span>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">
        <span id="emailErr"></span>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone">
        <span id="phoneErr"></span>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <span id="passwordErr"></span>

        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword">
        <span id="confirmPasswordErr"></span>

        <label for="branch_id">Primary Branch ID:</label>
        <input type="number" name="branch_id" id="branch_id">
        <span id="branchErr"></span>

        <button type="submit">Register</button>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>

</body>
</html>
