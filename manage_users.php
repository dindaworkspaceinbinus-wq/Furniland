<?php
session_start();
include "includes/connect.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "includes/header.php";

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Furniland</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<h2 class="page-title">Manage Users</h2>

<div class="manage-users-container">
    <table class="custom-table">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Role</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['dob']); ?></td>
            <td><?= htmlspecialchars($row['gender']); ?></td>
            <td><?= htmlspecialchars($row['role']); ?></td>
            <td>
                <?php if ($row['userID'] == $_SESSION['userID']) : ?>
                    <strong><i>Current Admin</i></strong>
                <?php else : ?>
                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="userID" value="<?= $row['userID']; ?>">
                        <button type="submit" class="btn-remove">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>

<?php include "includes/footer.php"; ?>

</body>
</html>