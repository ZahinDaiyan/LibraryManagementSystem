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
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $id = isset($_POST['id']) ? mysqli_real_escape_string($conn, $_POST['id']) : null;

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
        
        // Email uniqueness check
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) $errors['email'] = "This email is already in use";
    }

    if (count($errors) > 0) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        if ($id) {
            $_SESSION['admin_user_form_id'] = $id;
        }
        header("Location: AdminUserFormController.php");
        exit();
    }

    $branch_val = $branch_id == '' ? "NULL" : "'$branch_id'";

    if ($action === 'create') {
        $storedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password_hash, phone, role, branch_id, is_active, created_at) 
                VALUES ('$name', '$email', '$storedPassword', '$phone', '$role', $branch_val, 1, NOW())";
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);
            logAction($conn, $admin_id, "Created User", "users", $new_id, "Role: $role, Email: $email");
            $_SESSION['msg'] = "Account for $name ($role) created successfully";
        } else {
            $_SESSION['error'] = "Failed to create user: " . mysqli_error($conn);
        }
    } else {
        $sql = "UPDATE users 
                SET name = '$name', email = '$email', phone = '$phone', role = '$role', branch_id = $branch_val 
                WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            logAction($conn, $admin_id, "Updated User Info", "users", $id, "Updated $name");
            $_SESSION['msg'] = "User $name updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update user: " . mysqli_error($conn);
        }
    }

} elseif ($action === 'change_role') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $sql = "UPDATE users SET role = '$new_role' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        logAction($conn, $admin_id, "Changed User Role", "users", $id, "New Role: $new_role");
        $_SESSION['msg'] = "Role updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update role: " . mysqli_error($conn);
    }

} elseif ($action === 'toggle_status') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $res = mysqli_query($conn, "SELECT name, is_active FROM users WHERE id = '$id'");
    $user = mysqli_fetch_assoc($res);
    
    if ($user) {
        $new_status = $user['is_active'] ? 0 : 1;
        $sql = "UPDATE users SET is_active = '$new_status' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            logAction($conn, $admin_id, "Toggled User Status", "users", $id, "Target: " . $user['name'] . ", New Status: " . ($new_status ? 'Active' : 'Inactive'));
            $_SESSION['msg'] = "User status toggled successfully";
        } else {
            $_SESSION['error'] = "Failed to toggle status: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "User not found";
    }
}

Close($conn);
header('Location: AdminUserController.php');
exit();
