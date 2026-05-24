<?php
session_start();

require_once __DIR__ . '/../includes/i18n.php';

$lang = app_current_language();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../LoginView.php");
    exit();
}

$user = $_SESSION['user_data'] ?? null;
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['msg'], $_SESSION['error']);

if (!$user) {
    header("Location: dashboardView.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('member.profile.title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/member.css">
    <style>
        body.lang-bn {
            font-family: 'Noto Sans Bengali', var(--font-body, sans-serif);
        }

        .lang-switch {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-left: 12px;
        }

        .lang-switch a {
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 8px 12px;
            border-radius: 999px;
            text-decoration: none;
        }

        .lang-switch a.active {
            background: rgba(245, 158, 11, 0.14);
            color: #fff;
        }
    </style>
</head>
<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">

<h2><?= app_translate('member.profile.title') ?></h2>
<a href="dashboardView.php">← <?= app_translate('member.profile.back_dashboard') ?></a>
<div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>">
    <a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= app_language_url('ProfileView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
    <a class="<?= $lang === 'bn' ? 'active' : '' ?>" href="<?= app_language_url('ProfileView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
</div>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3><?= app_translate('member.profile.personal') ?></h3>
<form novalidate action="../../Controllers/ProfileUpdateController.php" method="POST" enctype="multipart/form-data" onsubmit="return validateProfileUpdate(this)">
    <input type="hidden" name="action" value="update_profile">
    
    <p>
        <?php if ($user['profile_pic']): ?>
            <img src="../../uploads/profiles/<?= $user['profile_pic'] ?>" alt="Profile Picture" width="100"><br>
        <?php endif; ?>
        <label><?= app_translate('member.profile.picture') ?></label><br>
        <input type="file" name="profile_pic">
    </p>

    <p>
        <label><?= app_translate('member.profile.name') ?></label><br>
        <input type="text" name="name" value="<?= $user['name'] ?>" required>
    </p>
    <p>
        <label><?= app_translate('member.profile.email') ?></label><br>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>
    </p>
    <p>
        <label><?= app_translate('member.profile.phone') ?></label><br>
        <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
    </p>
    <p>
        <label><?= app_translate('member.profile.branch') ?></label><br>
        <input type="text" value="<?= $user['branch_name'] ?> (<?= $user['branch_city'] ?>)" disabled>
    </p>
    <p>
        <label><?= app_translate('member.profile.status') ?></label><br>
        <b style="color: <?= $user['is_active'] ? 'green' : 'red' ?>;">
            <?= $user['is_active'] ? app_translate('member.profile.active') : app_translate('member.profile.inactive') ?>
        </b>
    </p>
    
    <button type="submit"><?= app_translate('member.profile.update') ?></button>
</form>

<hr>

<h3><?= app_translate('member.profile.change_password') ?></h3>
<form novalidate action="../../Controllers/ProfileUpdateController.php" method="POST" onsubmit="return validatePasswordChange(this)">
    <input type="hidden" name="action" value="change_password">
    <p>
        <label><?= app_translate('member.profile.current_password') ?></label><br>
        <input type="password" name="current_password" required>
    </p>
    <p>
        <label><?= app_translate('member.profile.new_password') ?></label><br>
        <input type="password" name="new_password" required>
    </p>
    <p>
        <label><?= app_translate('member.profile.confirm_password') ?></label><br>
        <input type="password" name="confirm_password" required>
    </p>
    <button type="submit"><?= app_translate('member.profile.save_password') ?></button>
</form>

<script src="../js/member_validation.js"></script>
<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>

