<?php
session_start();

require_once __DIR__ . '/includes/i18n.php';

$lang = app_current_language();
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('auth.register.title') ?></title>
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

        .password-section {
            margin-bottom: 6px;
        }

        .password-input-wrap {
            position: relative;
            width: 100%;
        }

        .password-input-wrap input {
            width: 100%;
            padding-right: 96px;
            box-sizing: border-box;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #cbd5e1;
            border-radius: 999px;
            padding: 6px 10px;
            min-width: 0;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .password-toggle:hover {
            color: #fff;
            background: rgba(148, 163, 184, 0.12);
        }

        .password-strength {
            margin-top: 10px;
            padding: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 14px;
            background: #10192a;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        .password-strength-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 0.92rem;
            color: #e5e7eb;
        }

        .password-strength-label {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .password-strength-bar {
            height: 10px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.18);
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            width: 0;
            border-radius: inherit;
            transition: width 0.2s ease, background-color 0.2s ease;
        }

        .password-checklist {
            list-style: none;
            padding: 0;
            margin: 12px 0 0;
            display: grid;
            gap: 8px;
        }

        .password-checklist li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        .password-checklist li::before {
            content: "";
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid rgba(148, 163, 184, 0.55);
            background: transparent;
            flex: 0 0 18px;
            box-sizing: border-box;
        }

        .password-checklist li.met {
            color: #86efac;
        }

        .password-checklist li.met::before {
            content: "\2713";
            color: #06281c;
            background: #22c55e;
            border-color: #22c55e;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .password-strength[data-strength="weak"] .password-strength-fill {
            width: 33%;
            background: #ef4444;
        }

        .password-strength[data-strength="medium"] .password-strength-fill {
            width: 66%;
            background: #f59e0b;
        }

        .password-strength[data-strength="strong"] .password-strength-fill {
            width: 100%;
            background: #22c55e;
        }

        .password-strength[data-strength="weak"] .password-strength-label {
            color: #f87171;
        }

        .password-strength[data-strength="medium"] .password-strength-label {
            color: #fbbf24;
        }

        .password-strength[data-strength="strong"] .password-strength-label {
            color: #4ade80;
        }
    </style>
</head>

<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">

<div class="auth-container">
    <div class="auth-topbar">
        <a href="<?= app_language_url('../index.php') ?>" style="color: #9a9fbf; text-decoration:none;">← <?= app_translate('auth.back_home') ?></a>
        <div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>">
            <a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= app_language_url('RegisterView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
            <a class="<?= $lang === 'bn' ? 'active' : '' ?>" href="<?= app_language_url('RegisterView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
        </div>
    </div>
    <h2 style="margin-bottom: 24px;"><?= app_translate('auth.register.title') ?></h2>

    <form
        action="../Controllers/RegisterController.php"
        method="POST"
        onsubmit="return validateRegister(this)"
        novalidate
    >

        <label for="name"><?= app_translate('auth.name') ?></label>
        <input type="text" name="name" id="name">
        <span id="nameErr"></span>

        <label for="email"><?= app_translate('auth.email') ?></label>
        <input type="email" name="email" id="email">
        <span id="emailErr"></span>

        <label for="phone"><?= app_translate('auth.phone') ?></label>
        <input type="text" name="phone" id="phone">
        <span id="phoneErr"></span>

        <label for="password"><?= app_translate('auth.password') ?></label>
        <div class="password-section">
            <div class="password-input-wrap">
                <input type="password" name="password" id="password" aria-describedby="passwordErr passwordStrength">
                <button type="button" class="password-toggle" id="passwordToggle" aria-label="Show password" aria-pressed="false">Show Password</button>
            </div>
            <span id="passwordErr"></span>
            <div class="password-strength" id="passwordStrength" data-strength="weak">
                <div class="password-strength-head">
                    <span>Password strength</span>
                    <span class="password-strength-label" id="passwordStrengthLabel">Weak</span>
                </div>
                <div class="password-strength-bar" aria-hidden="true">
                    <div class="password-strength-fill" id="passwordStrengthFill"></div>
                </div>
                <ul class="password-checklist" id="passwordChecklist">
                    <li data-rule="length">At least 8 characters</li>
                    <li data-rule="number">Contains at least one number</li>
                    <li data-rule="symbol">Contains at least one symbol</li>
                </ul>
            </div>
        </div>

        <label for="confirmPassword"><?= app_translate('auth.confirm_password') ?></label>
        <input type="password" name="confirmPassword" id="confirmPassword">
        <span id="confirmPasswordErr"></span>

        <label for="branch_id"><?= app_translate('auth.branch') ?></label>
        <input type="number" name="branch_id" id="branch_id">
        <span id="branchErr"></span>

        <button type="submit"><?= app_translate('auth.submit_register') ?></button>

        <div style="margin-top: 20px; text-align: center;">
            <span style="color: #9a9fbf; font-size: 0.9rem;"><?= app_translate('auth.have_account') ?></span>
            <a href="<?= app_language_url('LoginView.php') ?>" class="btn-outline" style="margin-top: 10px;"><?= app_translate('auth.login_link') ?></a>
        </div>

    </form>

    <p id="error"><?= isset($_SESSION['error']) ? $_SESSION['error'] : "" ?></p>
    <p id="msg"><?= isset($_SESSION['msg']) ? $_SESSION['msg'] : "" ?></p>
</div>

<script src="js/auth.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var confirmPasswordInput = document.getElementById('confirmPassword');
        var toggleButton = document.getElementById('passwordToggle');
        var strengthBox = document.getElementById('passwordStrength');
        var strengthLabel = document.getElementById('passwordStrengthLabel');
        var checklistItems = document.querySelectorAll('#passwordChecklist li');

        function hasNumber(value) {
            return /[0-9]/.test(value);
        }

        function hasSymbol(value) {
            return /[^A-Za-z0-9]/.test(value);
        }

        function updateStrength() {
            var value = passwordInput.value;
            var lengthOk = value.length >= 8;
            var numberOk = hasNumber(value);
            var symbolOk = hasSymbol(value);
            var strength = 'weak';

            if (lengthOk && numberOk && symbolOk) {
                strength = 'strong';
            } else if (lengthOk && (numberOk || symbolOk)) {
                strength = 'medium';
            }

            strengthBox.dataset.strength = strength;
            strengthLabel.textContent = strength.charAt(0).toUpperCase() + strength.slice(1);

            checklistItems.forEach(function (item) {
                var rule = item.dataset.rule;
                var met = false;

                if (rule === 'length') {
                    met = lengthOk;
                } else if (rule === 'number') {
                    met = numberOk;
                } else if (rule === 'symbol') {
                    met = symbolOk;
                }

                item.classList.toggle('met', met);
            });
        }

        function togglePassword() {
            var isHidden = passwordInput.type === 'password';
            var nextType = isHidden ? 'text' : 'password';

            passwordInput.type = nextType;
            if (confirmPasswordInput) {
                confirmPasswordInput.type = nextType;
            }
            toggleButton.textContent = isHidden ? 'Hide Password' : 'Show Password';
            toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            toggleButton.setAttribute('aria-pressed', String(isHidden));
        }

        passwordInput.addEventListener('input', updateStrength);
        toggleButton.addEventListener('click', togglePassword);

        updateStrength();
    });
</script>

</body>
</html>
