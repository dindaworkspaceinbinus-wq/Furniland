<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $deleteID = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE productID = ?");
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    $stmt->close();
    
    header("Location: manage_product.php");
    exit();
}

$search = $_GET['search'] ?? '';
$vendorFilter = $_GET['vendor'] ?? '';

$query = "
SELECT p.*, v.vendorName
FROM products p
JOIN vendors v ON p.vendorID = v.vendorID
WHERE p.productName LIKE '%$search%'
";

if ($vendorFilter != '') {
    $query .= " AND p.vendorID = '$vendorFilter'";
}

$products = mysqli_query($conn, $query);
$vendors = mysqli_query($conn, "SELECT * FROM vendors");
?>

<link rel="stylesheet" href="assets/style.css?v=6">

<main class="admin-page">

    <div class="page-header">
        <h1>Manage Products</h1>
        <a href="add_product.php" class="btn-add">+ Add Product</a>
    </div>

    <div class="filter-bar">
        <form method="get">
            <input type="text" name="search" placeholder="Search product..."
                   value="<?= htmlspecialchars($search) ?>">

            <select name="vendor">
                <option value="">All Vendors</option>
                <?php while ($v = mysqli_fetch_assoc($vendors)) { ?>
                    <option value="<?= $v['vendorID'] ?>"
                        <?= ($vendorFilter == $v['vendorID']) ? 'selected' : '' ?>>
                        <?= $v['vendorName'] ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table class="product-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Vendor</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = mysqli_fetch_assoc($products)) { ?>
            <tr>
                <td>
                    <img src="assets/images/<?= $p['image'] ?>" class="thumb">
                </td>
                <td><?= $p['productName'] ?></td>
                <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                <td><?= $p['vendorName'] ?></td>
                <td class="actions">
                    <a href="edit_product.php?id=<?= $p['productID'] ?>" class="edit-link">Edit</a><br>
                    <a href="?delete=<?= $p['productID'] ?>"
                       class="delete-link"
                       onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</main>

<?php include "includes/footer.php"; ?>