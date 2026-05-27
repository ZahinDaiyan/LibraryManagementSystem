<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../LoginView.php');
    exit();
}

$branches = $_SESSION['bm_branches'] ?? array();
$editBranch = $_SESSION['bm_edit_branch'] ?? null;
$errors = $_SESSION['form_errors'] ?? array();
$old = $_SESSION['old_data'] ?? array();
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['form_errors'], $_SESSION['old_data'], $_SESSION['msg'], $_SESSION['error']);

$title = $editBranch ? 'Edit Branch' : 'Add Branch';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Branches</title>
    <link rel="stylesheet" href="../css/manager.css">
</head>
<body>

<main class="manager-shell">
    <header class="manager-header">
        <div>
            <div class="manager-kicker">Branch Manager</div>
            <h2 class="manager-title">Manage Branch Profiles</h2>
            <p class="manager-subtitle">Add, edit, or activate branches under your oversight.</p>
        </div>
        <div class="manager-actions">
            <a class="manager-back-link" href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
        </div>
    </header>

    <?php if ($msg): ?>
        <div class="manager-note success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="manager-note error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="manager-panel">
        <h3><?= $title ?></h3>
        <form class="manager-form" novalidate action="../../Controllers/BranchManagerBranchActionController.php" method="POST" onsubmit="return validateBranchForm(this)">
            <input type="hidden" name="action" value="<?= $editBranch ? 'update' : 'create' ?>">
            <?php if ($editBranch): ?>
                <input type="hidden" name="id" value="<?= $editBranch['id'] ?>">
            <?php endif; ?>
            <p>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? $editBranch['name'] ?? '') ?>">
                <?php if (isset($errors['name'])) echo "<span class='form-errors'>" . htmlspecialchars($errors['name']) . "</span>"; ?>
            </p>
            <p>
                <label for="address">Address</label>
                <textarea name="address" id="address" rows="3"><?= htmlspecialchars($old['address'] ?? $editBranch['address'] ?? '') ?></textarea>
                <?php if (isset($errors['address'])) echo "<span class='form-errors'>" . htmlspecialchars($errors['address']) . "</span>"; ?>
            </p>
            <p>
                <label for="city">City</label>
                <input type="text" name="city" id="city" value="<?= htmlspecialchars($old['city'] ?? $editBranch['city'] ?? '') ?>">
                <?php if (isset($errors['city'])) echo "<span class='form-errors'>" . htmlspecialchars($errors['city']) . "</span>"; ?>
            </p>
            <p>
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old['phone'] ?? $editBranch['phone'] ?? '') ?>">
                <?php if (isset($errors['phone'])) echo "<span class='form-errors'>" . htmlspecialchars($errors['phone']) . "</span>"; ?>
            </p>
            <div class="form-actions">
                <button type="submit"><?= $editBranch ? 'Update Branch' : 'Create Branch' ?></button>
                <?php if ($editBranch): ?>
                    <a class="manager-outline-link" href="../../Controllers/BranchManagerBranchController.php">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="manager-table-wrap">
        <h3>Managed Branches</h3>
        <table class="manager-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Librarians</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                    <tr><td colspan="7">No managed branches found.</td></tr>
                <?php endif; ?>
                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?= htmlspecialchars($branch['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($branch['city'] ?? '') ?></td>
                        <td><?= htmlspecialchars($branch['address'] ?? '') ?></td>
                        <td><?= htmlspecialchars($branch['phone'] ?? '') ?></td>
                        <td><?= (int)($branch['librarian_count'] ?? 0) ?></td>
                        <td>
                            <span class="status-pill <?= !empty($branch['is_active']) ? 'is-active' : 'is-inactive' ?>">
                                <?= !empty($branch['is_active']) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="inline-actions">
                                <a class="manager-chip-link" href="../../Controllers/BranchManagerBranchController.php?id=<?= $branch['id'] ?>">Edit</a>
                                <a class="manager-chip-link" href="../../Controllers/BranchManagerBranchActionController.php?action=toggle_status&id=<?= $branch['id'] ?>" onclick="return confirm('Toggle this branch status?')">
                                    <?= !empty($branch['is_active']) ? 'Deactivate' : 'Activate' ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<script src="../js/branch_manager.js"></script>
</body>
</html>
