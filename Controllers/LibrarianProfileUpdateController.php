<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$_SESSION['error'] = '';
$_SESSION['msg'] = '';

$id = $_SESSION['id'];
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if ($name == '' || $email == '' || $phone == '') {
    $_SESSION['error'] = 'Please fill all required fields';
    header('Location: ../Controllers/LibrarianProfileController.php');
    exit();
}

$conn = Connect();
$existingUser = getUserByEmail($conn, $email);

if ($existingUser && $existingUser['id'] != $id) {
    Close($conn);
    $_SESSION['error'] = 'Email already exists';
    header('Location: ../Controllers/LibrarianProfileController.php');
    exit();
}

if ($newPassword != '' && $newPassword != $confirmPassword) {
    Close($conn);
    $_SESSION['error'] = 'Passwords do not match';
    header('Location: ../Controllers/LibrarianProfileController.php');
    exit();
}

$updateResult = updateUser($conn, $id, $name, $email, $phone);

if ($updateResult && $newPassword != '') {
    updateUserPassword($conn, $id, $newPassword);
}

$updatedProfile = getUserWithBranchById($conn, $id);
Close($conn);

$_SESSION['librarian'] = $updatedProfile;
$_SESSION['librarian_profile'] = $updatedProfile;
$_SESSION['msg'] = 'Profile updated successfully';

header('Location: ../Controllers/LibrarianProfileController.php');
exit();

?>