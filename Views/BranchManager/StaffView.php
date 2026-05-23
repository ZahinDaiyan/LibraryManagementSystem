<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Helpers\Url;

if (!class_exists(Url::class)) {
    require_once __DIR__ . '/../../app/Helpers/Url.php';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$branches = $_SESSION['bm_branches'] ?? array();
$assignable = $_SESSION['bm_assignable_librarians'] ?? array();
$managed = $_SESSION['bm_managed_librarians'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Assignment Hub</title>
    <link rel="stylesheet" href="<?= Url::asset('Views/css/manager.css') ?>">
    <style>
        .page-shell {
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.06);
        }

        .intro {
            color: #cbd5e1;
            margin-top: 8px;
            margin-bottom: 18px;
        }

        .flash-success,
        .flash-error {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-weight: 600;
        }

        .flash-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #10b981;
        }

        .flash-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #ef4444;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin: 18px 0 22px;
        }

        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(16, 24, 39, 0.55);
            border-radius: 12px;
            padding: 14px;
        }

        .stat-label {
            font-size: 0.78rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 6px;
        }

        .panel {
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 18px;
            background: rgba(15, 23, 42, 0.6);
        }

        .panel h3 {
            margin-top: 0;
            margin-bottom: 6px;
        }

        .panel-note {
            margin: 0 0 14px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .assign-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .search-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.45);
            color: #f8fafc;
        }

        .search-note {
            margin-top: 6px;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .search-note.error {
            color: #ef4444;
        }

        .field select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
        }

        .btn-assign {
            padding: 11px 20px;
            border-radius: 8px;
            border: 1px solid #f59e0b;
            background: #f59e0b;
            color: #0f172a;
            font-weight: 700;
            cursor: pointer;
        }

        .muted-list {
            margin: 0;
            padding-left: 18px;
            color: #cbd5e1;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .action-inline {
            margin: 0;
        }

        .btn-remove {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ef4444;
            background: transparent;
            color: #ef4444;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-remove:hover {
            background: #ef4444;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="page-shell">
    <div class="top-actions">
        <h2>Staff Assignment Hub</h2>
        <a class="back-link" href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
    </div>

    <p class="intro">Assign librarians to your branches, review current placements, and remove assignments when needed.</p>

    <?php if ($msg): ?>
        <div class="flash-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="flash-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Managed Branches</div>
            <div class="stat-value"><?= count($branches) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Assignable Librarians</div>
            <div class="stat-value"><?= count($assignable) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Currently Assigned</div>
            <div class="stat-value"><?= count($managed) ?></div>
        </div>
    </div>

    <div class="panel">
        <h3>Assign Librarian to Branch</h3>
        <p class="panel-note">Choose a librarian and the target branch, then submit to create or move assignment.</p>

        <form novalidate action="../../Controllers/BranchManagerStaffActionController.php" method="POST" onsubmit="return validateStaffAssignForm(this)">
            <input type="hidden" name="action" value="assign">

            <div class="assign-grid">
                <div class="field">
                    <label for="librarian_search">Search Librarian</label>
                    <input
                        type="text"
                        id="librarian_search"
                        class="search-input"
                        placeholder="Search by name, email, or current branch"
                        autocomplete="off"
                    >
                    <div id="librarian_search_note" class="search-note">Type to filter the librarian list below.</div>
                </div>

                <div class="field">
                    <label>Librarian</label>
                    <select id="librarian_id" name="librarian_id" required>
                        <option value="">Select librarian</option>
                        <?php foreach ($assignable as $librarian): ?>
                            <option
                                value="<?= $librarian['id'] ?>"
                                data-search="<?= htmlspecialchars(strtolower(trim(($librarian['name'] ?? '') . ' ' . ($librarian['email'] ?? '') . ' ' . ($librarian['branch_name'] ?? 'unassigned')))) ?>"
                            >
                                <?= htmlspecialchars($librarian['name'] ?? '') ?> (<?= htmlspecialchars($librarian['email'] ?? '') ?>)
                                <?= !empty($librarian['branch_name']) ? ' - current: ' . htmlspecialchars($librarian['branch_name']) : ' - current: unassigned' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Branch</label>
                    <select name="branch_id" required>
                        <option value="">Select branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name'] ?? '') ?> (<?= htmlspecialchars($branch['city'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <button type="submit" class="btn-assign">Assign Librarian</button>
                </div>
            </div>
        </form>
    </div>

    <div class="panel">
        <h3>Managed Branches</h3>
        <?php if (empty($branches)): ?>
            <p class="panel-note">No managed branches are currently assigned to your account.</p>
        <?php else: ?>
            <ul class="muted-list">
                <?php foreach ($branches as $branch): ?>
                    <li><?= htmlspecialchars($branch['name'] ?? '') ?> - <?= htmlspecialchars($branch['city'] ?? '') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h3>Current Branch Librarians</h3>
        <p class="panel-note">These librarians are currently assigned to branches you manage.</p>

        <div class="table-wrap">
            <table border="1" cellpadding="10" width="100%">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Branch</th>
                    <th>Action</th>
                </tr>
                <?php if (empty($managed)): ?>
                    <tr><td colspan="5">No librarians assigned to managed branches.</td></tr>
                <?php endif; ?>
                <?php foreach ($managed as $librarian): ?>
                    <tr>
                        <td><?= htmlspecialchars($librarian['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($librarian['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($librarian['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($librarian['branch_name'] ?? '') ?></td>
                        <td>
                            <form novalidate class="action-inline" action="../../Controllers/BranchManagerStaffActionController.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="librarian_id" value="<?= $librarian['id'] ?>">
                                <button type="submit" class="btn-remove" onclick="return confirm('Remove this librarian assignment?')">Remove Assignment</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<script src="../js/branch_manager.js"></script>
</body>
</html>
