<?php

session_start();

require_once __DIR__ . '/../app/Helpers/Url.php';
use App\Helpers\Url;

$id = $_POST['id'] ?? $_GET['id'] ?? $_SESSION['admin_user_form_id'] ?? '';
if (isset($_SESSION['admin_user_form_id'])) {
    unset($_SESSION['admin_user_form_id']);
}

if ($id !== '') {
    header('Location: ' . Url::route('/admin/users/' . $id . '/edit'), true, 302);
    exit();
}

header('Location: ' . Url::route('/admin/users/create'), true, 302);
exit();
