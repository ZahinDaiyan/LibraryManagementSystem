<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/UserModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = Connect();
$errors = [];

if ($action === 'create' || $action === 'update') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $role = $_POST['role'];
    $branch_id = $_POST['branch_id'];
    $id = $_POST['id'] ?? null;

    // Field-level Validation
    if (empty($name)) $errors['name'] = "Name is required";
    if (empty($email)) $errors['email'] = "Email is required";
    if (empty($phone)) $errors['phone'] = "Phone number is required";
    
    if ($action === 'create') {
        $password = $_POST['password'];
        if (empty($password)) $errors['password'] = "Password is required for new accounts";
        
        // Email uniqueness check
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) $errors['email'] = "This email is already in use";
    }
require_once '../Models/UserModel.php';
require_once '../Models/AuditModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = Connect();
$errors = [];
$admin_id = $_SESSION['id'];

if ($action === 'create' || $action === 'update') {
    // ... validation code ...
    if ($action === 'create') {
        // Direct creation of staff/member
        $storedPassword = password_hash($password, PASSWORD_DEFAULT);
        $branch_val = $branch_id == '' ? "NULL" : "'$branch_id'";
        $sql = "INSERT INTO users (name, email, password_hash, phone, role, branch_id, is_active, created_at) 
                VALUES ('$name', '$email', '$storedPassword', '$phone', '$role', $branch_val, 1, NOW())";
        mysqli_query($conn, $sql);
        $new_id = mysqli_insert_id($conn);
        logAction($conn, $admin_id, "Created User", "users", $new_id, "Role: $role, Email: $email");
        $_SESSION['msg'] = "Account for $name ($role) created successfully";
    } else {
        // Update user
        $branch_val = $branch_id == '' ? "NULL" : "'$branch_id'";
        $sql = "UPDATE users 
                SET name = '$name', email = '$email', phone = '$phone', role = '$role', branch_id = $branch_val 
                WHERE id = '$id'";
        mysqli_query($conn, $sql);
        logAction($conn, $admin_id, "Updated User Info", "users", $id, "Updated $name");
        $_SESSION['msg'] = "User $name updated successfully";
    }

} elseif ($action === 'change_role') {
    $id = $_POST['id'];
    $new_role = $_POST['role'];
    mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = '$id'");
    logAction($conn, $admin_id, "Changed User Role", "users", $id, "New Role: $new_role");
    $_SESSION['msg'] = "Role updated successfully";

} elseif ($action === 'toggle_status') {
    $id = $_GET['id'];
    $res = mysqli_query($conn, "SELECT name, is_active FROM users WHERE id = '$id'");
    $user = mysqli_fetch_assoc($res);
    $new_status = $user['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE users SET is_active = '$new_status' WHERE id = '$id'");
    logAction($conn, $admin_id, "Toggled User Status", "users", $id, "Target: " . $user['name'] . ", New Status: " . ($new_status ? 'Active' : 'Inactive'));
    $_SESSION['msg'] = "User status toggled successfully";
}


Close($conn);
header('Location: AdminUserController.php');
exit();
