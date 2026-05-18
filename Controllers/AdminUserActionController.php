<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';
require_once '../Models/AuditModel.php';

$action = $_POST['action'] ?? $_POST['action'] ?? '';
$conn = Connect();
$errors = [];
$admin_id = $_SESSION['id'];

if ($action === 'create' || $action === 'update') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $role = htmlspecialchars($_POST['role']);
    $branch_id = $_POST['branch_id'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Validation
    if (empty($name)) $errors['name'] = "Name is required";
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^[0-9+]{10,15}$/', $_POST['phone'])) {
        $errors['phone'] = "Phone number must be numeric (10-15 digits)";
    }
    
    if ($action === 'create') {
        $password = $_POST['password'];
        if (empty($password)) $errors['password'] = "Password is required for new accounts";
    }

    // Email uniqueness check
    if (isEmailTakenByOtherUser($conn, $email, $id)) {
        $errors['email'] = "This email is already in use";
    }

    if (count($errors) > 0) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        if ($id) {
            $_SESSION['admin_user_form_id'] = $id;
        }
        Close($conn);
        header("Location: AdminUserFormController.php");
        exit();
    }

    if ($action === 'create') {
        if (createAdminUser($conn, $name, $email, $password, $phone, $role, $branch_id)) {
            $new_id = mysqli_insert_id($conn);
            logAction($conn, $admin_id, "Created User", "users", $new_id, "Role: $role, Email: $email");
            $_SESSION['msg'] = "Account for $name ($role) created successfully";
        } else {
            $_SESSION['error'] = "Failed to create user";
        }
    } else {
        if (updateAdminUser($conn, $id, $name, $email, $phone, $role, $branch_id)) {
            logAction($conn, $admin_id, "Updated User Info", "users", $id, "Updated $name");
            $_SESSION['msg'] = "User $name updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update user";
        }
    }

} elseif ($action === 'change_role') {
    $id = $_POST['id'];
    $new_role = $_POST['role'];
    
    if (updateUserRole($conn, $id, $new_role)) {
        logAction($conn, $admin_id, "Changed User Role", "users", $id, "New Role: $new_role");
        $_SESSION['msg'] = "Role updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update role";
    }

} elseif ($action === 'toggle_status') {
    $id = $_POST['id'];
    $user = toggleUserStatus($conn, $id);
    
    if ($user) {
        logAction($conn, $admin_id, "Toggled User Status", "users", $id, "Target: " . $user['name'] . ", New Status: " . ($user['new_status'] ? 'Active' : 'Inactive'));
        $_SESSION['msg'] = "User status toggled successfully";
    } else {
        $_SESSION['error'] = "User not found or failed to toggle status";
    }
}

Close($conn);
header('Location: AdminUserController.php');
exit();
