<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>
    <main>
        <div class="home-title" style="text-align: left; margin-left: 50px;">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="dashboard-grid">
            <a href="manage_product.php" class="dashboard-card">
                <h3>Manage Products</h3>
                <p>View, edit, and add furniture products.</p>
            </a>
            
            <a href="manage_vendor.php" class="dashboard-card">
                <h3>Manage Vendors</h3>
                <p>View and manage furniture vendors.</p>
            </a>

            <a href="manage_users.php" class="dashboard-card">
                <h3>Manage Users</h3>
                <p>View and control user accounts.</p>
            </a>

            <a href="view_transactions.php" class="dashboard-card">
                <h3>View Transactions</h3>
                <p>Monitor purchase history and details.</p>
            </a>
        </div>

        <div class="dashboard-welcome">
            <h2 style="margin-bottom: 10px; color: #ffffff;">Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p style="color: #b0b8c1;">Use the cards above to manage the platform. Keep track of users, inventory, vendors, and transactions efficiently.</p>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>