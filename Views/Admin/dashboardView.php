<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$stats = $_SESSION['admin_stats'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <title>Admin Dashboard</title>
    <style>
        /* Premium Stat Card Styling */
        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .stat-card {
            background: #1e2235 !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 12px !important;
            padding: 20px 16px !important;
            width: 220px !important;
            text-align: center !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease !important;
        }

        .stat-card:hover {
            border-color: rgba(255, 255, 255, 0.3) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25) !important;
        }

        .stat-card h4 {
            color: #9a9fbf !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            margin-bottom: 8px !important;
        }

        .stat-card .stat-value {
            font-size: 28px !important;
            font-weight: 700 !important;
            color: #f0f2ff !important;
            margin-bottom: 0 !important;
        }

        /* High-visibility tailored stats colors */
        .stat-card.blue-theme .stat-value {
            color: #60a5fa !important; /* Premium light blue */
        }

        .stat-card.red-theme .stat-value {
            color: #f87171 !important; /* Premium pastel coral red */
        }

        .stat-card.green-theme .stat-value {
            color: #34d399 !important; /* Premium emerald green */
        }

        /* Modern Navigation Card Grid */
        .admin-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
            gap: 20px !important;
            padding: 0 !important;
            list-style: none !important;
            margin-top: 16px !important;
        }

        .admin-grid li {
            margin-bottom: 0 !important;
        }

        .admin-grid li a {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            height: 100% !important;
            padding: 24px !important;
            border-radius: 12px !important;
            background: #1e2235 !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }

        .admin-grid li a:hover {
            transform: translateY(-4px) !important;
            border-color: #f59e0b !important;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15) !important;
            background: rgba(245, 158, 11, 0.03) !important;
        }

        .admin-grid .card-title {
            color: #f59e0b !important;
            font-size: 1.15rem !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
            transition: color 0.2s ease !important;
        }

        .admin-grid li a:hover .card-title {
            color: #fbbf24 !important;
        }

        .admin-grid .card-desc {
            color: #9a9fbf !important;
            font-size: 0.85rem !important;
            line-height: 1.45 !important;
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (Platform Admin)</p>
<hr>

<h3>Platform-Wide Statistics</h3>

<div class="stats-container">
    
    <div class="stat-card">
        <h4>Total Members</h4>
        <p class="stat-value"><?= $stats['total_members'] ?? 0 ?></p>
    </div>

    <div class="stat-card">
        <h4>Books in Catalog</h4>
        <p class="stat-value"><?= $stats['total_books'] ?? 0 ?></p>
    </div>

    <div class="stat-card blue-theme">
        <h4>Active Loans</h4>
        <p class="stat-value"><?= $stats['total_active_loans'] ?? 0 ?></p>
    </div>

    <div class="stat-card red-theme">
        <h4>Overdue Loans</h4>
        <p class="stat-value"><?= $stats['total_overdue_loans'] ?? 0 ?></p>
    </div>

    <div class="stat-card green-theme">
        <h4>Outstanding Fines</h4>
        <p class="stat-value">$<?= number_format($stats['total_fines_outstanding'] ?? 0, 2) ?></p>
    </div>

</div>

<hr>

<h3>Navigation</h3>
<ul class="admin-grid">
    <li>
        <a href="../../Controllers/AdminUserController.php">
            <span class="card-title">Manage All Users</span>
            <span class="card-desc">Add, edit, or disable branch manager, librarian, and member accounts.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminBookCatalogController.php">
            <span class="card-title">Master Book Catalog</span>
            <span class="card-desc">Catalog-wide book inventory, title details, and metadata management.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminBranchController.php">
            <span class="card-title">Manage Branches</span>
            <span class="card-desc">Oversee library branches, assign branch managers, and modify policies.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminTransferController.php">
            <span class="card-title">Inter-Branch Transfers</span>
            <span class="card-desc">Review, approve, and track movements of book copies between branch locations.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminComplaintController.php">
            <span class="card-title">Member Complaints</span>
            <span class="card-desc">Read and resolve complaints, feedback, and support queries submitted by members.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminAuditLogController.php">
            <span class="card-title">Platform Audit Logs</span>
            <span class="card-desc">Track and inspect system-wide administrative transactions and security actions.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminAnnouncementController.php">
            <span class="card-title">Platform Announcements</span>
            <span class="card-desc">Publish branch-wide or system-wide news alerts to user dashboards.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminSettingsController.php">
            <span class="card-title">Global System Settings</span>
            <span class="card-desc">Configure library rules, database settings, and core application constants.</span>
        </a>
    </li>
    <li>
        <a href="../../Controllers/AdminReportController.php">
            <span class="card-title">Platform Reports</span>
            <span class="card-desc">Analyze health metrics, loan ratios, fines history, and branch productivity stats.</span>
        </a>
    </li>
</ul>

<br>
<a href="../../Controllers/LogoutController.php"><button style="margin-top: 10px;">Logout</button></a>

</body>
</html>
