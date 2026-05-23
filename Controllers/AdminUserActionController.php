<?php

session_start();

require_once __DIR__ . '/../app/Helpers/Url.php';
use App\Helpers\Url;

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = $_POST['id'] ?? $_GET['id'] ?? '';

if ($action === 'change_role') {
    header('Location: ' . Url::route('/admin/users/' . $id . '/change-role'), true, 302);
    exit();
} elseif ($action === 'toggle_status') {
    header('Location: ' . Url::route('/admin/users/' . $id . '/toggle-status'), true, 302);
    exit();
} elseif ($action === 'delete_user') {
    header('Location: ' . Url::route('/admin/users/' . $id . '/delete'), true, 302);
    exit();
} elseif ($action === 'create') {
    header('Location: ' . Url::route('/admin/users'), true, 302);
    exit();
} elseif ($action === 'update') {
    header('Location: ' . Url::route('/admin/users/' . $id), true, 302);
    exit();
}

header('Location: ' . Url::route('/admin/users'), true, 302);
exit();
