<?php
session_start();
include "includes/connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$userID = $_POST['userID'];

if ($userID == $_SESSION['userID']) {
    header("Location: manage_users.php");
    exit;
}

mysqli_query($conn, "DELETE FROM users WHERE userID = $userID");

header("Location: manage_users.php");
exit;