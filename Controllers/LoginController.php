<?php
session_start();
require_once "../config/Database.php";

if(isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE email = ? AND role = 'branch_manager' AND is_active = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows == 1) {
        $stmt->bind_result($id, $password_hash, $role);
        $stmt->fetch();
        if(password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: DashboardController.php");
            exit;
        } else {
            header("Location: ../views/login.php?error=Incorrect+password");
            exit;
        }
    } else {
        header("Location: ../views/login.php?error=No+active+Branch+Manager+found");
        exit;
    }
} elseif(isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}
?>