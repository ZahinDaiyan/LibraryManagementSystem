<?php
// gethint.php - returns HTML suggestions for member names
header('Content-Type: text/html; charset=utf-8');
session_start();

require_once '../Models/DB.php';
require_once '../Models/LibrarianWorkflowModel.php';

if (!isset($_SESSION['role'])) {
    echo '';
    exit();
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') { echo ''; exit(); }

$conn = Connect();
$branchInfo = isset($_SESSION['id']) ? getLibrarianBranchByUserId($conn, $_SESSION['id']) : array();
$branchId = isset($branchInfo['branch_id']) ? (int)$branchInfo['branch_id'] : 0;
$term = mysqli_real_escape_string($conn, $q);
$sql = "SELECT id, name, email, phone FROM users WHERE role = 'member' AND branch_id = '$branchId' AND (name LIKE '%$term%' OR email LIKE '%$term%' OR phone LIKE '%$term%') ORDER BY name LIMIT 10";
$res = mysqli_query($conn, $sql);
$html = '';
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $name = htmlspecialchars($r['name']);
        $email = htmlspecialchars($r['email']);
        $phone = htmlspecialchars($r['phone']);
        // escape single quotes for inline onclick
        $safeName = str_replace("'", "\\'", $name);
        $html .= "<div style=\"padding:6px;cursor:pointer;color:#e8eaf0;background:#1a1d2e;margin-bottom:6px;border-radius:6px;\" onclick=\"selectMemberSuggestion('" . $safeName . "');\">";
        $html .= "<strong style='display:block;color:#f0f2ff'>" . $name . "</strong>";
        $html .= "<small style='color:#9a9fbf'>" . $email . " · " . $phone . "</small>";
        $html .= "</div>";
    }
}

Close($conn);
echo $html;
exit();

?>
