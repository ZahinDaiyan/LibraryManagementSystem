<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/i18n.php';

$lang = app_current_language();
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('auth.login.title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css?v=<?= time() ?>">
    <style>
        body.lang-bn {
            font-family: 'Noto Sans Bengali', var(--font-body);
        }

        .auth-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .lang-switch {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .lang-switch a {
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #c7d2fe;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .lang-switch a.active {
            background: rgba(245, 158, 11, 0.12);
            color: #fff;
            border-color: rgba(245, 158, 11, 0.45);
        }

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

<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">

<div class="auth-container">
    <div class="auth-topbar">
        <a href="<?= app_language_url('../index.php') ?>" style="color: #9a9fbf; text-decoration:none;">← <?= app_translate('auth.back_home') ?></a>
        <div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>">
            <a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= app_language_url('LoginView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
            <a class="<?= $lang === 'bn' ? 'active' : '' ?>" href="<?= app_language_url('LoginView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
        </div>
    </div>
    <h2 style="margin-bottom: 24px;"><?= app_translate('auth.login.title') ?></h2>

    <form
        action="../Controllers/LoginController.php"
        method="POST"
        onsubmit="return validateLogin(this)"
        novalidate
    >

        <label for="email"><?= app_translate('auth.email') ?></label>
        <input type="email" name="email" id="email">
        <span id="emailErr"></span>

        <label for="password"><?= app_translate('auth.password') ?></label>
        <input type="password" name="password" id="password">
        <span id="passwordErr"></span>

        <button type="submit"><?= app_translate('auth.submit_login') ?></button>
        
        <div style="margin-top: 20px; text-align: center;">
            <span style="color: #9a9fbf; font-size: 0.9rem;"><?= app_translate('auth.no_account') ?></span>
            <a href="<?= app_language_url('RegisterView.php') ?>" class="btn-outline" style="margin-top: 10px;"><?= app_translate('auth.create_account') ?></a>
        </div>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>

</body>
</html>
