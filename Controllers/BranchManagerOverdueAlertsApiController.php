<?php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    http_response_code(403);
    echo json_encode(array('success' => false, 'message' => 'Access denied', 'alerts' => array()));
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/BranchManagerModel.php';

if (!isset($_GET['threshold_days'])) {
    $thresholdDays = 7;
} elseif (filter_var($_GET['threshold_days'], FILTER_VALIDATE_INT) === false) {
    echo json_encode(array('success' => false, 'message' => 'Threshold must be an integer from 0 to 365', 'alerts' => array()));
    exit();
} else {
    $thresholdDays = (int)$_GET['threshold_days'];
}

if ($thresholdDays < 0 || $thresholdDays > 365) {
    echo json_encode(array('success' => false, 'message' => 'Threshold must be between 0 and 365 days', 'alerts' => array()));
    exit();
}

$conn = Connect();
$alerts = bmGetOverdueAlerts($conn, $_SESSION['id'], $thresholdDays);
Close($conn);

echo json_encode(array(
    'success' => true,
    'threshold_days' => $thresholdDays,
    'alerts' => $alerts
));
exit();

?>
