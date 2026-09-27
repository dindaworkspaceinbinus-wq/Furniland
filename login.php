<?php
session_start();
include "includes/connect.php";
include "includes/header.php";


if (isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit;
}

if (isset($_COOKIE['userID'])) {
    $cookieUserID = (int) $_COOKIE['userID'];

    $stmt = $conn->prepare("SELECT userID, username, role FROM users WHERE userID = ?");
    $stmt->bind_param("i", $cookieUserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: home.php");
        exit;
    }
    $stmt->close();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($email === '') {
        $errors[] = "Email must be filled.";
    }

    if ($password === '') {
        $errors[] = "Password must be filled.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT userID, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    setcookie('userID', $user['userID'], time() + (7 * 24 * 60 * 60), '/', '', false, true);
                    setcookie('rememberEmail', $email, time() + (7 * 24 * 60 * 60), '/', '', false, false);
                }

                if ($user['role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit;
            } else {
                $errors[] = "Password doesn't match.";
            }
        } else {
            $errors[] = "Email not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>

<body>
<main class="login-page">
    <div class="login-box">
        <h2>Login</h2>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?=
                       isset($_POST['email'])
                           ? htmlspecialchars($_POST['email'])
                           : (isset($_COOKIE['rememberEmail']) ? htmlspecialchars($_COOKIE['rememberEmail']) : '')
                   ?>" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <div class="remember-row">
                <input type="checkbox" name="remember"
                       <?= isset($_COOKIE['rememberEmail']) ? 'checked' : '' ?>>
                <label>Remember me</label>
            </div>

            <?php if (!empty($errors)) { ?>
                <div style="margin-top:15px;">
                    <?php foreach ($errors as $e) { ?>
                        <p style="color:#ff5252;text-align:center;margin-bottom:10px;"><?= $e ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <button type="submit" class="login-btn">Login</button>

            <p class="register-text">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</main>

<?php include "includes/footer.php"; ?>
</body>
</html>
