<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'branch_manager') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$_SESSION['error'] = '';
$_SESSION['msg'] = '';

$id = $_SESSION['id'];
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name == '' || $email == '' || $phone == '') {
    $_SESSION['error'] = 'Please fill all required fields';
    header('Location: ../Controllers/BranchManagerProfileController.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email format';
    header('Location: ../Controllers/BranchManagerProfileController.php');
    exit();
}

$conn = Connect();
$existingUser = getUserByEmail($conn, $email);

if ($existingUser && $existingUser['id'] != $id) {
    Close($conn);
    $_SESSION['error'] = 'Email already exists';
    header('Location: ../Controllers/BranchManagerProfileController.php');
    exit();
}

if ($newPassword != '' && $newPassword != $confirmPassword) {
    Close($conn);
    $_SESSION['error'] = 'Passwords do not match';
    header('Location: ../Controllers/BranchManagerProfileController.php');
    exit();
}

$updated = updateUser($conn, $id, $name, $email, $phone);

if ($updated && $newPassword != '') {
    updateUserPassword($conn, $id, $newPassword);
}

Close($conn);

if ($updated) {
    $_SESSION['name'] = $name;
    $_SESSION['msg'] = 'Profile updated successfully';
} else {
    $_SESSION['error'] = 'Unable to update profile';
}

header('Location: ../Controllers/BranchManagerProfileController.php');
exit();

?>
