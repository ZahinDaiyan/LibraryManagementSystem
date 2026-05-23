<?php

session_start();

require_once __DIR__ . '/../app/Helpers/Url.php';
use App\Helpers\Url;

$search = $_POST['search'] ?? $_GET['search'] ?? '';
$role_filter = $_POST['role_filter'] ?? $_GET['role_filter'] ?? '';

$queryString = '';
if ($search !== '' || $role_filter !== '') {
    $queryString = '?' . http_build_query([
        'search' => $search,
        'role_filter' => $role_filter
    ]);
}

header('Location: ' . Url::route('/admin/users' . $queryString), true, 302);
exit();
