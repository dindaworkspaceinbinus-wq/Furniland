<?php
    session_start();
    include "includes/connect.php";
    include "includes/header.php";

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $vendor = isset($_GET['vendor']) ? $_GET['vendor'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

    $query = "SELECT p.*, v.vendorName, v.location 
            FROM products p 
            JOIN vendors v ON p.vendorID = v.vendorID 
            WHERE 1=1";

    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $query .= " AND (p.productName LIKE '%$search%' OR p.description LIKE '%$search%')";
    }

    if (!empty($vendor)) {
        $vendor = $conn->real_escape_string($vendor);
        $query .= " AND v.vendorID = '$vendor'";
    }

    if ($sort == 'price_asc') {
        $query .= " ORDER BY p.price ASC";
    } elseif ($sort == 'price_desc') {
        $query .= " ORDER BY p.price DESC";
    } else {
        $query .= " ORDER BY p.productID DESC";
    }

    $result = $conn->query($query);

    $vendorQuery = "SELECT * FROM vendors ORDER BY vendorName ASC";
    $vendorResult = $conn->query($vendorQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniland - Product Catalog</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>

<body>
    <div class="header-pc">
        <h1>Product Catalog</h1>
        <p>Browse, filter, and sort furniture</p>
    </div>

    <div class="filter-bar">
        <form method="GET" action="product_catalog.php" style="display: contents;">
            <div>
                <select name="vendor" onchange="this.form.submit()">
                    <option value="">All Vendors</option>
                    <?php
                    if ($vendorResult && $vendorResult->num_rows > 0) {
                        while ($vendorRow = $vendorResult->fetch_assoc()) {
                            $selected = ($vendor == $vendorRow['vendorID']) ? 'selected' : '';
                            echo '<option value="' . $vendorRow['vendorID'] . '" ' . $selected . '>' 
                                 . htmlspecialchars($vendorRow['vendorName']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <select name="sort" onchange="this.form.submit()">
                    <option value="">Sort by</option>
                    <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Lowest Price</option>
                    <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Highest Price</option>
                </select>
            </div>

            <div class="search">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>

    <div class="product-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $formattedPrice = "Rp " . number_format($row['price'], 0, ',', '.');
                
                $imagePath = !empty($row['image']) ? "assets/images/" . $row['image'] : "assets/megaminx.webp";
                
                $description = strlen($row['description']) > 60 
                    ? substr($row['description'], 0, 60) . '...' 
                    : $row['description'];
        ?>
                <div class="product-card-catalog">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                    <div class="product-content">
                        <div class="product-title"><?php echo htmlspecialchars($row['productName']); ?></div>
                        <div class="product-desc"><?php echo htmlspecialchars($description); ?></div>
                        <div style="font-size: 12px; color: #6b9ac4; margin: 5px 0;">
                            Vendor: <?php echo htmlspecialchars($row['vendorName']); ?>
                        </div>
                        <div class="product-price"><?php echo $formattedPrice; ?></div>
                        <a href="product-detail.php?id=<?php echo $row['productID']; ?>">
                            <button>View Detail</button>
                        </a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p style="text-align: center; color: #b0b8c1; grid-column: 1/-1; padding: 40px;">No products found matching your criteria.</p>';
        }
        ?>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>

<?php
$conn->close();
?>