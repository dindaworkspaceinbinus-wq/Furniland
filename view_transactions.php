<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Transactions | Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>

<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$sql = "
    SELECT 
        t.transactionID,
        u.username,
        t.totalPrice,
        t.transactionDate
    FROM transactions t
    JOIN users u ON t.userID = u.userID
    ORDER BY t.transactionDate DESC
";

$result = mysqli_query($conn, $sql);
?>

<h2 class="page-title">All Transactions</h2>

<div class="history-container">
    <table class="custom-table">
        <tr>
            <th>Transaction ID</th>
            <th>User</th>
            <th>Total Price</th>
            <th>Transaction Date</th>
        </tr>

        <?php if (mysqli_num_rows($result) == 0): ?>
            <tr>
                <td colspan="4">No transactions found.</td>
            </tr>
        <?php endif; ?>

        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td>#<?= $row['transactionID']; ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td>Rp <?= number_format($row['totalPrice'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars(date('d M Y', strtotime($row['transactionDate']))); ?></td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>

<?php include "includes/footer.php"; ?>

</body>
</html>