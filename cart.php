<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_item'])) {
    $cartID = intval($_POST['cart_id']);
    $deleteQuery = "DELETE FROM cart WHERE cartID = $cartID AND userID = $userID";
    if ($conn->query($deleteQuery)) {
        header("Location: cart.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['checkout'])) {
    $queryCart = "SELECT c.*, p.price FROM cart c JOIN products p ON c.productID = p.productID WHERE c.userID = $userID";
    $resultCart = $conn->query($queryCart);

    if ($resultCart && $resultCart->num_rows > 0) {
        $totalPrice = 0;
        $items = [];

        while ($row = $resultCart->fetch_assoc()) {
            $subtotal = $row['price'] * $row['quantity'];
            $totalPrice += $subtotal;
            $items[] = [
                'productID' => $row['productID'],
                'quantity' => $row['quantity'],
                'subtotal' => $subtotal
            ];
        }

        $date = date("Y-m-d");
        $insertTrans = "INSERT INTO transactions (userID, totalPrice, transactionDate) VALUES ($userID, $totalPrice, '$date')";
        
        if ($conn->query($insertTrans)) {
            $transactionID = $conn->insert_id;

            foreach ($items as $item) {
                $pID = $item['productID'];
                $qty = $item['quantity'];
                $sub = $item['subtotal'];
                $conn->query("INSERT INTO transaction_details (transactionID, productID, quantity, subtotal) VALUES ($transactionID, $pID, $qty, $sub)");
            }

            $conn->query("DELETE FROM cart WHERE userID = $userID");

            header("Location: history.php");
            exit();
        }
    }
}

$queryDisplay = "SELECT c.cartID, p.productName, p.price, c.quantity 
                 FROM cart c 
                 JOIN products p ON c.productID = p.productID 
                 WHERE c.userID = $userID";
$resultDisplay = $conn->query($queryDisplay);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>
    <main>
        <div class="page-title">
            <h1>Your Cart</h1>
            <?php if ($resultDisplay && $resultDisplay->num_rows == 0): ?>
                <p style="color: #b0b8c1; margin-top: 10px;">Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <?php if ($resultDisplay && $resultDisplay->num_rows > 0): ?>
            <div class="cart-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0;
                        while ($row = $resultDisplay->fetch_assoc()): 
                            $subtotal = $row['price'] * $row['quantity'];
                            $grandTotal += $subtotal;
                            $formattedSub = "Rp " . number_format($subtotal, 0, ',', '.');
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['productName']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $formattedSub; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cartID']; ?>">
                                    <button type="submit" name="delete_item" class="btn-remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <form method="POST" action="">
                    <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
    <?php include "includes/footer.php"; ?>
</body>
</html>

<?php $conn->close(); ?>