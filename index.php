<?php

session_start();

if (isset($_SESSION['role'])) {

    $role = $_SESSION['role'];

    if ($role === 'admin') {

        header("Location: controllers/AdminController.php");
        exit();

    } elseif ($role === 'branch_manager') {

      //manager controller
      
      
    } elseif ($role === 'librarian') {

      // librarian controller

    } elseif ($role === 'member') {

        header("Location: Controllers/DashboardController.php");
        exit();
    }

} else {

    header("Location: views/LoginView.php");
    exit();
}
?>
