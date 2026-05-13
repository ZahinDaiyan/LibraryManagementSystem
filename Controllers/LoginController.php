<?php

require_once '../models/DB.php';
require_once '../models/UserModel.php';

session_start();

$_SESSION['error'] = "";

$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);

if ($email == "" || $password == "") {

    $_SESSION['error'] = "Please fill all fields";

    header('Location: ../views/auth/LoginView.php');
    die();
}

$conn = Connect();

$user = login($conn, $email, $password);

Close($conn);

if ($user) {

    $_SESSION['id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header('Location: ../index.php');

} else {

    $_SESSION['error'] = "Invalid Credentials";

    header('Location: ../views/auth/LoginView.php');

}
?>