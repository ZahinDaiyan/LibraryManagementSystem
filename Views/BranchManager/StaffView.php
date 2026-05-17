<?php
session_start();

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
    <title>Assign Librarians</title>
</head>
<body>

<h2>Assign Librarians to Branches</h2>
<a href="../../Controllers/BranchManagerDashboardController.php">Back to Dashboard</a>
<hr>

<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>Assign Librarian</h3>
<form novalidate action="../../Controllers/BranchManagerStaffActionController.php" method="POST" onsubmit="return validateStaffAssignForm(this)">
    <input type="hidden" name="action" value="assign">
    <p>
        <label>Librarian:</label><br>
        <select name="librarian_id">
            <option value="">Select librarian</option>
            <?php foreach ($assignable as $librarian): ?>
                <option value="<?= $librarian['id'] ?>">
                    <?= htmlspecialchars($librarian['name'] ?? '') ?> (<?= htmlspecialchars($librarian['email'] ?? '') ?>)
                    <?= !empty($librarian['branch_name']) ? ' - currently ' . htmlspecialchars($librarian['branch_name']) : ' - unassigned' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>Branch:</label><br>
        <select name="branch_id">
            <option value="">Select branch</option>
            <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name'] ?? '') ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <button type="submit">Assign</button>
</form>

<hr>

<h3>Current Branch Librarians</h3>
<table border="1" cellpadding="8" width="100%">
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
                <form novalidate action="../../Controllers/BranchManagerStaffActionController.php" method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="librarian_id" value="<?= $librarian['id'] ?>">
                    <button type="submit" onclick="return confirm('Remove this librarian assignment?')">Remove Assignment</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script src="../../js/branch_manager.js"></script>
</body>
</html>
