<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $name = trim($_POST['vendorName']);
    $location = trim($_POST['location']);

    if ($name && $location && strlen($name) <= 20 && strlen($location) <= 100) {
        mysqli_query($conn, "INSERT INTO vendors (vendorName, location)
                             VALUES ('$name', '$location')");
        header("Location: manage_vendor.php");
        exit();
    }
}
?>

<link rel="stylesheet" href="assets/style.css?v=4">

<main class="form-page">

    <div class="form-card">
        <h2>Add Vendor</h2>

        <form method="post">
            <label>Vendor Name</label>
            <input type="text" name="vendorName" maxlength="20" required>

            <label>Location</label>
            <input type="text" name="location" maxlength="100" required>

            <button type="submit" name="submit" class="form-btn">Add Vendor</button>
        </form>
    </div>

</main>

<?php include "includes/footer.php"; ?>
