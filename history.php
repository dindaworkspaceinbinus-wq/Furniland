<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

$queryTrans = "SELECT * FROM transactions WHERE userID = $userID ORDER BY transactionDate DESC, transactionID DESC";
$resultTrans = $conn->query($queryTrans);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>
    <main>
        <div class="page-title">
            <h1>Transaction History</h1>
            <?php if ($resultTrans && $resultTrans->num_rows == 0): ?>
                <p style="color: #b0b8c1; margin-top: 10px;">You haven't made any transactions yet.</p>
            <?php endif; ?>
        </div>

        <div class="history-container">
            <?php 
            if ($resultTrans && $resultTrans->num_rows > 0):
                while ($trans = $resultTrans->fetch_assoc()): 
                    $transID = $trans['transactionID'];
                    $date = date("d M Y", strtotime($trans['transactionDate']));
                    $total = "Rp " . number_format($trans['totalPrice'], 0, ',', '.');
            ?>
                <div class="history-card">
                    <div class="history-header">
                        <div class="history-info">
                            <strong>Transaction ID: <?php echo $transID; ?></strong>
                            <span>Date: <?php echo $date; ?></span>
                        </div>
                        <div class="history-total">
                            Total: <?php echo $total; ?>
                        </div>
                    </div>

                    <table class="custom-table" style="border: none;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryDetail = "SELECT td.*, p.productName 
                                            FROM transaction_details td 
                                            JOIN products p ON td.productID = p.productID 
                                            WHERE td.transactionID = $transID";
                            $resultDetail = $conn->query($queryDetail);
                            
                            while ($item = $resultDetail->fetch_assoc()):
                                $itemSub = "Rp " . number_format($item['subtotal'], 0, ',', '.');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['productName']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo $itemSub; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php 
                endwhile; 
            endif;
            ?>
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
</body>
</html>

<?php $conn->close(); ?>