<?php
session_start();

$_SESSION = array();

session_destroy();
if (isset($_COOKIE['userID'])) {
    setcookie('userID', '', time() - 3600, '/');
}

header("Location: home.php");
exit;
?>