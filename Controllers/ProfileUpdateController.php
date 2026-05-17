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

        // --- File Validation Start ---
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];

        // Get real MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_mime = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate MIME type
        if (!in_array($file_mime, $allowed_mime_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.";
            Close($conn);
            header('Location: ProfileController.php');
            exit();
        }

        // Validate extension matches
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = "Invalid file extension. Allowed: " . implode(', ', $allowed_extensions);
            Close($conn);
            header('Location: ProfileController.php');
            exit();
        }

        // Validate file size
        if ($file_size > $max_file_size) {
            $_SESSION['error'] = "File is too large. Maximum size is 2MB.";
            Close($conn);
            header('Location: ProfileController.php');
            exit();
        }

        // Verify it's a real image
        if (!getimagesize($file_tmp)) {
            $_SESSION['error'] = "Uploaded file is not a valid image.";
            Close($conn);
            header('Location: ProfileController.php');
            exit();
        }
        // --- File Validation End ---

        $uploadDir = '../uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file_ext;
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($file_tmp, $uploadFile)) {
            $profile_pic = $fileName;
        } else {
            $_SESSION['error'] = "Failed to save the uploaded file. Please try again.";
            Close($conn);
            header('Location: ProfileController.php');
            exit();
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
