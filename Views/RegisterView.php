<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="css/auth.css?v=<?= time() ?>">
    <style>
        .btn-outline {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            border: 1px solid #f59e0b;
            color: #f59e0b;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 0;
        }
        .btn-outline:hover {
            background: #f59e0b;
            color: #fff;
        }
    </style>
</head>

<body>

<div class="auth-container">
    <h2 style="margin-bottom: 24px;">Member Registration</h2>

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

        <div style="margin-top: 20px; text-align: center;">
            <span style="color: #9a9fbf; font-size: 0.9rem;">Already have an account?</span>
            <a href="LoginView.php" class="btn-outline" style="margin-top: 10px;">Login</a>
        </div>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>

</body>
</html>
