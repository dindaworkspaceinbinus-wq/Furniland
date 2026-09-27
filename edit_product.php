<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_product.php");
    exit;
}

$productID = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE productID = ?");
$stmt->bind_param("i", $productID);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

$vendors = [];
$result = $conn->query("SELECT vendorID, vendorName FROM vendors");
while ($row = $result->fetch_assoc()) {
    $vendors[] = $row;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['productName']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $vendorID = $_POST['vendorID'];

    if (empty($name) || strlen($name) < 3 || strlen($name) > 30) {
        $errors[] = "Product name must be 3–30 characters.";
    }

    if (empty($desc)) {
        $errors[] = "Description must be filled.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Price must be numeric and greater than 0.";
    }

    if (empty($vendorID)) {
        $errors[] = "Vendor must be selected.";
    }

    $imageName = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed)) {
            if ($product['image'] && file_exists("assets/images/" . $product['image'])) {
                unlink("assets/images/" . $product['image']);
            }

            $imageName = time() . "_" . $fileName;
            move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $imageName);
        } else {
            $errors[] = "Image must be JPG or PNG.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE products
            SET productName = ?, description = ?, price = ?, image = ?, vendorID = ?
            WHERE productID = ?
        ");
        $stmt->bind_param(
            "ssdsii",
            $name,
            $desc,
            $price,
            $imageName,
            $vendorID,
            $productID
        );

        if ($stmt->execute()) {
            header("Location: manage_product.php"); 
            exit;
        } else {
            $errors[] = "Failed to update product.";
        }
    }
}
?>

<link rel="stylesheet" href="assets/style.css">

<main class="admin-form-page">
    <div class="admin-form-box">
        <h2>Edit Product</h2>

        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="productName"
                   value="<?= htmlspecialchars($product['productName']) ?>" required>

            <label>Description</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Price (IDR)</label>
            <input type="number" name="price" step="0.01"
                   value="<?= $product['price'] ?>" required>

            <label>Vendor</label>
            <select name="vendorID" required>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?= $v['vendorID'] ?>"
                        <?= $v['vendorID'] == $product['vendorID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['vendorName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Current Image</label>
            <?php if ($product['image']): ?>
                <img src="assets/images/<?= htmlspecialchars($product['image']) ?>"
                     style="width:120px; margin:10px 0; border-radius:8px;">
            <?php else: ?>
                <p style="font-size:13px;color:#aaa;">No image uploaded</p>
            <?php endif; ?>

            <label>Change Image (optional)</label>
            <input type="file" name="image" accept="image/png, image/jpeg">

            <button type="submit" class="admin-form-btn">Update Product</button>
        </form>
    </div>
</main>

<?php include "includes/footer.php"; ?>