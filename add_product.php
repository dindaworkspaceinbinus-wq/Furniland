<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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

    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'png'];

        if (in_array($ext, $allowed)) {
            $imageName = time() . "_" . $fileName;
            move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $imageName);
        } else {
            $errors[] = "Image must be JPG or PNG.";
        }
    } else {
        $errors[] = "Please upload an image.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO products (productName, description, price, image, vendorID)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssdsi",
            $name,
            $desc,
            $price,
            $imageName,
            $vendorID
        );

        if ($stmt->execute()) {
            header("Location: manage_product.php");
            exit;
        } else {
            $errors[] = "Failed to insert product.";
        }
    }
}
?>

<link rel="stylesheet" href="assets/style.css">

<main class="admin-form-page">
    <div class="admin-form-box">
        <h2>Add New Product</h2>

        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="productName" required>

            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Price (IDR)</label>
            <input type="number" name="price" step="0.01" required>

            <label>Vendor</label>
            <select name="vendorID" required>
                <option value="">-- Select Vendor --</option>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?= $v['vendorID'] ?>">
                        <?= htmlspecialchars($v['vendorName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Product Image (JPG / PNG)</label>
            <input type="file" name="image" accept="image/png, image/jpeg">

            <button type="submit" class="admin-form-btn">Update Product</button>
        </form>
    </div>
</main>

<?php include "includes/footer.php"; ?>