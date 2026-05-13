<?php 
session_start();
$role = $_SESSION['role']
  if (isset($role)) {
    if (role === 'admin') {
      // redirect to admin controller
    }
    if (role === 'branch-manager') {
      // redirect to manager controller
    }
    if (role === 'librariyan') {
      // redirect to librariyan controller
    }
    if (role === 'member') {
      // redirect to member controller
    }
  } else {
    // redirect to loginview 
  }
?>
