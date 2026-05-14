<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$action = $_POST['action'] ?? '';
$id = $_SESSION['id'];
$conn = Connect();

if ($action === 'update_profile') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
            $profile_pic = $fileName;
        }
    }

    if (updateUser($conn, $id, $name, $email, $phone, $profile_pic)) {
        $_SESSION['name'] = $name;
        $_SESSION['msg'] = "Profile updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update profile";
    }

} elseif ($action === 'change_password') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $user = getUserById($conn, $id);
    if ($user && (password_verify($currentPassword, $user['password_hash']) || $currentPassword === $user['password_hash'])) {
        if ($newPassword === $confirmPassword) {
            if (updateUserPassword($conn, $id, $newPassword)) {
                $_SESSION['msg'] = "Password changed successfully";
            } else {
                $_SESSION['error'] = "Failed to change password";
            }
        } else {
            $_SESSION['error'] = "New passwords do not match";
        }
    } else {
        $_SESSION['error'] = "Incorrect current password";
    }
}

Close($conn);
header('Location: ProfileController.php');
exit();
