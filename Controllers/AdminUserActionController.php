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

    // Validation
    if (empty($name)) $errors['name'] = "Name is required";
    if (empty($email)) $errors['email'] = "Email is required";
    if (empty($phone)) $errors['phone'] = "Phone is required";
    
    if ($action === 'create') {
        $password = $_POST['password'];
        if (empty($password)) $errors['password'] = "Password is required";
        
        // Check email uniqueness
        $existing = getUserByEmail($conn, $email);
        if ($existing) $errors['email'] = "Email already registered";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header('Location: AdminUserFormController.php' . ($id ? "?id=$id" : ""));
        exit();
    }

    if ($action === 'create') {
        createUser($conn, $name, $email, $password, $phone, $role, '', $branch_id);
        $_SESSION['msg'] = "User created successfully";
    } else {
        // Updated updateUser in UserModel to handle role and branch
        $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', role = '$role', branch_id = " . ($branch_id == '' ? "NULL" : "'$branch_id'") . " WHERE id = '$id'";
        mysqli_query($conn, $sql);
        $_SESSION['msg'] = "User updated successfully";
    }

} elseif ($action === 'toggle_status') {
    $id = $_GET['id'];
    $user = getUserById($conn, $id);
    $new_status = $user['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE users SET is_active = '$new_status' WHERE id = '$id'");
    $_SESSION['msg'] = "User status updated";
}

Close($conn);
header('Location: AdminUserController.php');
exit();
