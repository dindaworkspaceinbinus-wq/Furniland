<?php
session_start();
include "includes/connect.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $vendorID = mysqli_real_escape_string($conn, $_GET['id']);

    $checkQuery = "SELECT * FROM products WHERE vendorID = '$vendorID'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>
                alert('Cannot delete vendor: There are products associated with this vendor.');
                window.location.href = 'manage_vendor.php'; 
              </script>";
    } else {
        $deleteQuery = "DELETE FROM vendors WHERE vendorID = '$vendorID'";
        
        if (mysqli_query($conn, $deleteQuery)) {
            echo "<script>
                    alert('Vendor deleted successfully.');
                    window.location.href = 'manage_vendor.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: manage_vendor.php");
    exit();
}
?>