<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

$query = "SELECT p.*, v.vendorName, v.location 
          FROM products p 
          JOIN vendors v ON p.vendorID = v.vendorID 
          ORDER BY p.productID DESC
          LIMIT 6";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniland - Home</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>

<body>
    <main>
        <div class="home-title">
            <h1>Furniland</h1>
            <p>Furnitures you might love</p>
        </div>

        <div class="product-list">
            <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $formattedPrice = "Rp " . number_format($row['price'], 0, ',', '.');
                        $imagePath = !empty($row['image']) ? "assets/images/" . $row['image'] : "assets/megaminx.webp";
                        $description = strlen($row['description']) > 80 
                        ? substr($row['description'], 0, 80) . '...' 
                        : $row['description'];
                    ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                        <h3><?php echo htmlspecialchars($row['productName']); ?></h3>
                        <p style="margin-top: 2px;"><?php echo htmlspecialchars($row['vendorName']); ?></p>
                        <p><?php echo htmlspecialchars($description); ?></p>
                        <h4><?php echo $formattedPrice; ?></h4>
                        
                        <a href="product-detail.php?id=<?php echo $row['productID']; ?>">
                             <button>View Details</button>
                        </a>
                    </div>
            <?php
                }
            } else {
                echo '<p style="text-align: center; color: #b0b8c1; grid-column: 1/-1;">No products available at the moment.</p>';
            }
            ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>

<?php
$conn->close();
?>