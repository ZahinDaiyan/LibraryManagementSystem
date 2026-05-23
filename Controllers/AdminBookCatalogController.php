<?php

session_start();

$search = $_POST['search'] ?? '';

// This compatibility wrapper keeps legacy dashboard links working while the
// admin book catalog resource migrates to the new routing layer.
$redirect = '../admin/books';
if ($search !== '') {
    $redirect .= '?search=' . urlencode($search);
}

header('Location: ' . $redirect);
exit();
