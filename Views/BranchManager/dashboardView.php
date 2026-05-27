<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$profile = $_SESSION['branch_manager_profile'] ?? array();
$dashboard = $_SESSION['bm_dashboard'] ?? array();
$branches = $dashboard['branches'] ?? array();
$stats = $dashboard['stats'] ?? array();
$pendingRenewals = $dashboard['pending_renewals'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Manager Dashboard</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<main class="manager-shell">
    <header class="manager-header">
        <div>
            <div class="manager-kicker">Branch Manager</div>
            <h2 class="manager-title">Branch Manager Dashboard</h2>
            <p class="manager-subtitle">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? $profile['name'] ?? '') ?></p>
        </div>
        <div class="manager-actions">
            <a class="manager-secondary-link" href="../../Controllers/LogoutController.php">Logout</a>
        </div>
    </header>

    <?php if ($msg): ?>
        <div class="manager-note success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="manager-note error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="manager-section">
        <h3>Managed Branch Summary</h3>
        <div class="metric-grid">
            <div class="manager-card metric-card">
                <span class="metric-label">Branches</span>
                <span class="metric-value"><?= (int)($stats['total_branches'] ?? 0) ?></span>
            </div>
            <div class="manager-card metric-card">
                <span class="metric-label">Librarians</span>
                <span class="metric-value"><?= (int)($stats['total_librarians'] ?? 0) ?></span>
            </div>
            <div class="manager-card metric-card">
                <span class="metric-label">Active Loans</span>
                <span class="metric-value is-accent"><?= (int)($stats['total_active_loans'] ?? 0) ?></span>
            </div>
            <div class="manager-card metric-card">
                <span class="metric-label">Overdue Loans</span>
                <span class="metric-value is-danger"><?= (int)($stats['total_overdue_loans'] ?? 0) ?></span>
            </div>
            <div class="manager-card metric-card">
                <span class="metric-label">Outstanding Fines</span>
                <span class="metric-value is-success">$<?= number_format((float)($stats['total_outstanding_fines'] ?? 0), 2) ?></span>
            </div>
        </div>
    </section>

    <section class="manager-table-wrap">
        <h3>Pending Loan Renewal Approvals</h3>
        <table class="manager-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Loan ID</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Branch</th>
                    <th>Current Due</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingRenewals)): ?>
                    <tr><td colspan="7">No renewal requests pending branch manager review.</td></tr>
                <?php endif; ?>
                <?php foreach ($pendingRenewals as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['id']) ?></td>
                        <td><?= htmlspecialchars($r['loan_id']) ?></td>
                        <td><?= htmlspecialchars($r['member_name']) ?></td>
                        <td><?= htmlspecialchars($r['book_title']) ?></td>
                        <td><?= htmlspecialchars($r['branch_name']) ?></td>
                        <td><?= htmlspecialchars($r['due_date']) ?></td>
                        <td>
                            <div class="action-group">
                                <form method="POST" action="../../Controllers/RenewalDecisionController.php">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($r['id']) ?>">
                                    <input type="hidden" name="decision" value="approved">
                                    <button class="btn-approve" type="submit">Approve</button>
                                </form>
                                <form method="POST" action="../../Controllers/RenewalDecisionController.php">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($r['id']) ?>">
                                    <input type="hidden" name="decision" value="rejected">
                                    <button class="btn-reject" type="submit">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="manager-links">
        <h3>Navigation</h3>
        <ul>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerProfileController.php">Manage Profile</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerBranchController.php">Manage Branch Profiles</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerStaffController.php">Assign Librarians</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerPolicyController.php">Configure Branch Policies</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerReportController.php">Cross-Branch Reports</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerTransferController.php">Inter-Branch Transfers</a></li>
            <li><a class="manager-nav-link" href="../../Controllers/BranchManagerAnnouncementController.php">Platform Announcements</a></li>
        </ul>
    </section>

    <section class="manager-table-wrap">
        <h3>Branches Under Oversight</h3>
        <table class="manager-table">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>City</th>
                    <th>Librarians</th>
                    <th>Active Loans</th>
                    <th>Overdue</th>
                    <th>Outstanding Fines</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                    <tr><td colspan="7">No branches are assigned to your manager account yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($branches as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['city'] ?? '') ?></td>
                        <td><?= (int)($b['librarian_count'] ?? 0) ?></td>
                        <td><?= (int)($b['active_loans'] ?? 0) ?></td>
                        <td><?= (int)($b['overdue_loans'] ?? 0) ?></td>
                        <td>$<?= number_format((float)($b['outstanding_fines'] ?? 0), 2) ?></td>
                        <td>
                            <span class="status-pill <?= !empty($b['is_active']) ? 'is-active' : 'is-inactive' ?>">
                                <?= !empty($b['is_active']) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>
