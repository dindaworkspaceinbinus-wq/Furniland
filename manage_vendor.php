<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM vendors");
?>

<link rel="stylesheet" href="assets/style.css?v=4">

<main class="admin-page">

    <div class="page-header">
        <h1>Manage Vendors</h1>
        <a href="add_vendor.php" class="btn-add">+ Add Vendor</a>
    </div>

    <table class="vendor-table">
        <thead>
            <tr>
                <th>Vendor Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($v = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $v['vendorName'] ?></td>
                <td><?= $v['location'] ?></td>
                <td class="actions">
                    <a href="edit_vendor.php?id=<?= $v['vendorID'] ?>" class="edit-link">Edit</a><br>
                    <a href="delete_vendor.php?id=<?= $v['vendorID'] ?>" class="delete-link"
                       onclick="return confirm('Delete this vendor?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</main>

<?php include "includes/footer.php"; ?>
