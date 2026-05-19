<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../Views/LoginView.php");
    exit();
}

require_once '../models/DB.php';
require_once '../models/ReservationModel.php';

$conn = Connect();
$reservations = getMemberReservations($conn, $_SESSION['id']);
Close($conn);

$_SESSION['reservations'] = $reservations;

header("Location: ../Views/Member/ReservationsView.php");
exit();
