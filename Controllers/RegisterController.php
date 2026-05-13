<?php

require_once '../models/DB.php';
require_once '../models/UserModel.php';

session_start();

$_SESSION['error'] = "";
$_SESSION['msg'] = "";

$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$password = htmlspecialchars($_POST['password']);
$confirmPassword = htmlspecialchars($_POST['confirmPassword']);
$branch_id = htmlspecialchars($_POST['branch_id']);

if (
    $name == "" ||
    $email == "" ||
    $phone == "" ||
    $password == "" ||
    $confirmPassword == ""
) {

    $_SESSION['error'] = "Please fill all fields";

    header('Location: ../views/auth/RegisterView.php');
    die();
}

if ($password != $confirmPassword) {

    $_SESSION['error'] = "Passwords do not match";

    header('Location: ../views/auth/RegisterView.php');
    die();
}

$conn = Connect();

$user = getUserByEmail($conn, $email);

if ($user) {

    $_SESSION['error'] = "Email already exists";

    Close($conn);

    header('Location: ../views/auth/RegisterView.php');
    die();
}

createUser(
    $conn,
    $name,
    $email,
    $password,
    $phone,
    "member",
    "",
    $branch_id
);

Close($conn);

$_SESSION['msg'] = "Registration Successful";

header('Location: ../views/auth/LoginView.php');

?>