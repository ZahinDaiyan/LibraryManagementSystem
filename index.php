<?php

session_start();

if (isset($_SESSION['role'])) {

    $role = $_SESSION['role'];

    if ($role === 'admin') {

        header("Location: Controllers/AdminDashboardController.php");
        exit();

    } elseif ($role === 'branch_manager') {

      //manager controller
      
      
    } elseif ($role === 'librarian') {

      header("Location: Controllers/LibrarianDashboardController.php");
      exit();

    } elseif ($role === 'member') {

        header("Location: Controllers/MemberDashboardController.php");
        exit();
    }

} else {

    header("Location: views/LoginView.php");
    exit();
}
?>
