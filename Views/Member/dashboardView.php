<?php
session_start();

require_once __DIR__ . '/../includes/i18n.php';

$lang = app_current_language();

if (!isset($_SESSION['role'])) {
    header('Location: ../LoginView.php');
    exit();
}

if ($_SESSION['role'] != 'member') {
    header('Location: ../../index.php');
    exit();
}

$announcements = $_SESSION['announcements'] ?? [];
$notifications = $_SESSION['notifications'] ?? [];
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_translate('member.dashboard.title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/member.css?v=<?= time() ?>">
    <style>
        body.lang-bn {
            font-family: 'Noto Sans Bengali', var(--font-body, sans-serif);
        }

        /* Premium announcement cards with high contrast readability */
        .announcement-card {
            background: #1a1d2e !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: 4px solid #f59e0b !important;
            border-radius: 8px !important;
            padding: 16px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15) !important;
        }

        .announcement-card h4 {
            color: #f59e0b !important; /* Golden amber heading for hierarchy */
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
        }

        .announcement-card p {
            color: #e8eaf0 !important; /* Crisp high-contrast text */
            font-size: 0.95rem !important;
            margin-bottom: 12px !important;
            line-height: 1.5 !important;
        }

        .announcement-card small {
            color: #9a9fbf !important; /* Readable gray for metadata */
            font-size: 0.8rem !important;
            font-weight: 400 !important;
        }

        /* Golden outline button style for Mark as Read */
        .btn-link {
            background: transparent !important;
            border: 1px solid #f59e0b !important;
            color: #f59e0b !important;
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            margin-left: 10px !important;
            display: inline-block !important;
        }

        .btn-link:hover {
            background: #f59e0b !important;
            color: #fff !important;
        }

        .member-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .lang-switch {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .lang-switch a {
            color: #f59e0b;
            text-decoration: none;
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 8px 12px;
            border-radius: 999px;
        }

        .lang-switch a.active {
            background: rgba(245, 158, 11, 0.14);
            color: #fff;
        }
    </style>
</head>
<body class="<?= $lang === 'bn' ? 'lang-bn' : 'lang-en' ?>">

<div class="member-topbar">
    <div>
        <h2><?= app_translate('member.dashboard.title') ?></h2>
        <p><?= app_translate('member.dashboard.welcome', array('name' => htmlspecialchars($_SESSION['name']))) ?></p>
    </div>
    <div class="lang-switch" aria-label="<?= app_translate('language.switch') ?>">
        <a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= app_language_url('dashboardView.php', 'en') ?>"><?= app_translate('language.english') ?></a>
        <a class="<?= $lang === 'bn' ? 'active' : '' ?>" href="<?= app_language_url('dashboardView.php', 'bn') ?>"><?= app_translate('language.bengali') ?></a>
    </div>
</div>

<hr>

<?php if (!empty($notifications)): ?>
    <div style="background-color: #fff3cd; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 20px;">
        <h3><?= app_translate('member.dashboard.notifications') ?></h3>
        <ul>
            <?php foreach ($notifications as $n): ?>
                <li>
                    <?= htmlspecialchars($n['message']) ?> 
                    <form method="POST" action="../../Controllers/NotificationActionController.php" style="display:inline;"><input type="hidden" name="id" value="<?= $n['id'] ?>"><button type="submit" class="btn-link"><?= app_translate('member.action.read') ?></button></form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 40px;">

    <div style="flex: 1;">
        <h3><?= app_translate('member.dashboard.features') ?></h3>
        <ul>
            <li><a href="../../Controllers/BookIndexController.php"><?= app_translate('member.feature.browse') ?></a></li>
            <li><a href="../../Controllers/MyLoansController.php"><?= app_translate('member.feature.loans') ?></a></li>
            <li><a href="../../Controllers/BorrowHistoryController.php"><?= app_translate('member.feature.history') ?></a></li>
            <li><a href="../../Controllers/ReservationController.php"><?= app_translate('member.feature.reservations') ?></a></li>
            <li><a href="../../Controllers/ReadingListController.php"><?= app_translate('member.feature.reading') ?></a></li>
            <li><a href="../../Controllers/ProfileController.php"><?= app_translate('member.feature.profile') ?></a></li>
            <li><a href="../../Controllers/FineController.php"><?= app_translate('member.feature.fines') ?></a></li>
            <li><a href="../../Controllers/MemberComplaintController.php"><?= app_translate('member.feature.support') ?></a></li>
        </ul>
        <br>
        <a href="../../Controllers/LogoutController.php"><button><?= app_translate('member.dashboard.logout') ?></button></a>
    </div>

    <div style="flex: 2; border-left: 1px solid #ccc; padding-left: 20px;">
        <h3><?= app_translate('member.dashboard.announcements') ?></h3>
        <?php if (empty($announcements)): ?>
            <p><?= app_translate('member.dashboard.none') ?></p>
        <?php endif; ?>
        <?php foreach ($announcements as $a): ?>
            <div class="announcement-card">
                <h4><?= htmlspecialchars($a['title'] ?? '') ?></h4>
                <p><?= htmlspecialchars($a['body'] ?? '') ?></p>
                <small>By <?= htmlspecialchars($a['author_name'] ?? '') ?> on <?= htmlspecialchars($a['published_at'] ?? '') ?></small>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script src="../js/member_ajax.js?v=<?= time() ?>"></script>
</body>
</html>
