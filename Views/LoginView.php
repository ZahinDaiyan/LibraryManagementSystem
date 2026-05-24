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
    <title>Login</title>
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
    <a href="../index.php" style="display:inline-block; margin-bottom: 18px; color: #9a9fbf; text-decoration:none;">← Back to home</a>
    <h2 style="margin-bottom: 24px;">Login</h2>

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
        
        <div style="margin-top: 20px; text-align: center;">
            <span style="color: #9a9fbf; font-size: 0.9rem;">Don't have an account?</span>
            <a href="RegisterView.php" class="btn-outline" style="margin-top: 10px;">Create New Account</a>
        </div>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>

</body>
</html>
