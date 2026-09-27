<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT p.*, v.vendorName, v.location 
          FROM products p 
          JOIN vendors v ON p.vendorID = v.vendorID 
          WHERE p.productID = $productID";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $formattedPrice = "Rp " . number_format($product['price'], 0, ',', '.');
    $imagePath = !empty($product['image']) ? "assets/images/" . $product['image'] : "assets/megaminx.webp";
} else {
    header("Location: catalog.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }
    
    $userID = $_SESSION['userID'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    $checkCart = "SELECT * FROM cart WHERE userID = $userID AND productID = $productID";
    $cartResult = $conn->query($checkCart);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        $updateCart = "UPDATE cart SET quantity = quantity + $quantity WHERE userID = $userID AND productID = $productID";
        if ($conn->query($updateCart)) {
            $successMessage = "Product quantity updated in cart!";
        }
    } else {
        $insertCart = "INSERT INTO cart (userID, productID, quantity) VALUES ($userID, $productID, $quantity)";
        if ($conn->query($insertCart)) {
            $successMessage = "Product added to cart!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['productName']); ?> - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>

<body>
    <div class="detail-container">
        <div class="detail-box">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['productName']); ?>">

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['productName']); ?></h1>
                <p class="product-desc">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <div class="product-price"><?php echo $formattedPrice; ?></div>

                <div class="product-meta">Vendor: <span><?php echo htmlspecialchars($product['vendorName']); ?></span></div>
                <div class="product-meta">Location: <span><?php echo htmlspecialchars($product['location']); ?></span></div>


                <form method="POST" action="">
                    <div class="quantity-box">
                        Quantity:
                        <input type="number" name="quantity" value="1" min="1" max="99" required>
                    </div>

                    <?php if (isset($_SESSION['userID']) && $_SESSION['role'] == 'member'): ?>
                        <button type="submit" name="add_to_cart" class="add-cart-btn">Add to Cart</button>
                    <?php elseif (!isset($_SESSION['userID'])): ?>
                        <a href="login.php" style="text-decoration: none;">
                            <button type="button" class="add-cart-btn">Add to Cart</button>
                        </a>
                    <?php else: ?>
                        <button type="button" class="add-cart-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                            Members Only
                        </button>
                    <?php endif; ?>

                    <?php if (isset($successMessage)): ?>
                    <div style="bcolor: #fff; margin: 15px 0; text-align: center; font-size: 14px;">
                        <?php echo $successMessage;
                        header("Location: cart.php");
                        exit; ?>   
                    </div>
                <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>

<?php
$conn->close();
?>