<?php

session_start();

$expectsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!function_exists('memberProfileUpdateRespond')) {
    function memberProfileUpdateRespond($expectsJson, $success, $message, $statusCode = 200)
    {
        if ($expectsJson) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
            echo json_encode(array(
                'success' => (bool)$success,
                'message' => $message,
                'redirect' => 'ProfileController.php'
            ));
            exit();
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        header('Location: ProfileController.php');
        exit();
    }
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    if ($expectsJson) {
        memberProfileUpdateRespond($expectsJson, false, 'Unauthorized', 403);
    }

    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$action = $_POST['action'] ?? '';
$id = $_SESSION['id'];
$conn = Connect();
$success = false;
$message = 'Invalid profile action';

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
            Close($conn);
            memberProfileUpdateRespond($expectsJson, false, "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.", 422);
        }

        // Validate extension matches
        if (!in_array($file_ext, $allowed_extensions)) {
            Close($conn);
            memberProfileUpdateRespond($expectsJson, false, "Invalid file extension. Allowed: " . implode(', ', $allowed_extensions), 422);
        }

        // Validate file size
        if ($file_size > $max_file_size) {
            Close($conn);
            memberProfileUpdateRespond($expectsJson, false, "File is too large. Maximum size is 2MB.", 422);
        }

        // Verify it's a real image
        if (!getimagesize($file_tmp)) {
            Close($conn);
            memberProfileUpdateRespond($expectsJson, false, "Uploaded file is not a valid image.", 422);
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
            Close($conn);
            memberProfileUpdateRespond($expectsJson, false, "Failed to save the uploaded file. Please try again.", 500);
        }
    }

    if (updateUser($conn, $id, $name, $email, $phone, $profile_pic)) {
        $_SESSION['name'] = $name;
        $success = true;
        $message = "Profile updated successfully";
    } else {
        $success = false;
        $message = "Failed to update profile";
    }

} elseif ($action === 'change_password') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $user = getUserById($conn, $id);
    if ($user && (password_verify($currentPassword, $user['password_hash']) || $currentPassword === $user['password_hash'])) {
        if ($newPassword === $confirmPassword) {
            if (updateUserPassword($conn, $id, $newPassword)) {
                $success = true;
                $message = "Password changed successfully";
            } else {
                $success = false;
                $message = "Failed to change password";
            }
        } else {
            $success = false;
            $message = "New passwords do not match";
        }
    } else {
        $success = false;
        $message = "Incorrect current password";
    }
}

Close($conn);
memberProfileUpdateRespond($expectsJson, $success, $message, $success ? 200 : 400);
