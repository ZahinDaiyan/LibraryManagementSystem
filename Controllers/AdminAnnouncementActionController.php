<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Views/LoginView.php');
    exit();
}

require_once '../Models/DB.php';
require_once '../Models/AuditModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = Connect();
$errors = [];
$admin_id = $_SESSION['id'];

if ($action === 'create' || $action === 'update') {
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    $branch_id = $_POST['branch_id'];
    $id = $_POST['id'] ?? null;

    if (empty($title)) $errors['title'] = "Title is required";
    if (empty($body)) $errors['body'] = "Content is required";

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header('Location: AdminAnnouncementFormController.php' . ($id ? "?id=$id" : ""));
        exit();
    }

    $branch_val = $branch_id == '' ? "NULL" : "'$branch_id'";

    if ($action === 'create') {
        $sql = "INSERT INTO announcements (title, body, branch_id, author_id, published_at) 
                VALUES ('$title', '$body', $branch_val, '$admin_id', NOW())";
        mysqli_query($conn, $sql);
        $new_id = mysqli_insert_id($conn);
        logAction($conn, $admin_id, "Created Announcement", "announcements", $new_id, "Title: $title");
        $_SESSION['msg'] = "Announcement posted successfully.";
    } else {
        $sql = "UPDATE announcements 
                SET title = '$title', body = '$body', branch_id = $branch_val 
                WHERE id = '$id'";
        mysqli_query($conn, $sql);
        logAction($conn, $admin_id, "Updated Announcement", "announcements", $id, "Title: $title");
        $_SESSION['msg'] = "Announcement updated successfully.";
    }

} elseif ($action === 'delete') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM announcements WHERE id = '$id'");
    logAction($conn, $admin_id, "Deleted Announcement", "announcements", $id);
    $_SESSION['msg'] = "Announcement deleted.";
}

Close($conn);
header('Location: AdminAnnouncementController.php');
exit();
