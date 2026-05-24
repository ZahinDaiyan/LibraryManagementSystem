<?php

session_start();

require_once 'Views/includes/i18n.php';

app_current_language();

$redirectPath = 'Views/HomeView.php';

if (isset($_GET['lang']) && $_GET['lang'] !== '') {
  $redirectPath .= '?lang=' . rawurlencode($_GET['lang']);
}

if (isset($_SESSION['role'])) {

    $role = $_SESSION['role'];

    if ($role === 'admin') {

        header("Location: Controllers/AdminDashboardController.php");
        exit();

    } elseif ($role === 'branch_manager' || $role === 'manager') {

      header("Location: Controllers/BranchManagerDashboardController.php");
      exit();

    } elseif ($role === 'librarian') {

      header("Location: Controllers/LibrarianDashboardController.php");
      exit();

    } elseif ($role === 'member') {

        header("Location: Controllers/MemberDashboardController.php");
        exit();
    }

} else {

  header("Location: " . $redirectPath);
    exit();
}
?>
