<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $rePassword = $_POST['re-password'] ?? '';
    $gender     = $_POST['gender'] ?? '';
    $dob        = $_POST['dob'] ?? '';

    if ($username === '') {
        $errors[] = "Username must be filled.";
    } elseif (strlen($username) < 4 || strlen($username) > 20) {
        $errors[] = "Username must be between 4 and 20 characters.";
    }

    if ($email === '') {
        $errors[] = "Email must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (!str_ends_with($email, "@gmail.com")) {
        $errors[] = "Email must end with '@gmail.com'.";
    }

    if ($password === '') {
        $errors[] = "Password must be filled.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($rePassword === '') {
        $errors[] = "Please re-enter your password.";
    } elseif ($password !== $rePassword) {
        $errors[] = "Passwords do not match.";
    }

    if ($gender === '') {
        $errors[] = "Please select your gender.";
    }

    if ($dob === '') {
        $errors[] = "Date of Birth must be filled.";
    } else {
        $birthDate = strtotime($dob);
        $today = strtotime(date("Y-m-d"));

        if ($birthDate >= $today) {
            $errors[] = "Date of Birth must be a valid date in the past.";
        }
    }

    if (empty($errors)) {
        $checkUsername = $conn->prepare("SELECT userID FROM users WHERE username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $checkUsername->store_result();
        
        if ($checkUsername->num_rows > 0) {
            $errors[] = "Username already exists.";
        }
        $checkUsername->close();
    }

    if (empty($errors)) {
        $checkEmail = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();
        
        if ($checkEmail->num_rows > 0) {
            $errors[] = "Email already registered.";
        }
        $checkEmail->close();
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'member'; 
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, gender, dob, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $gender, $dob, $role);
        
        if ($stmt->execute()) {
            $_SESSION['userID'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            
            $stmt->close();
            $conn->close();
            
            header("Location: product_catalog.php");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
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
    <title>Register Page</title>
    <link rel="stylesheet" href="assets/style.css?v=3"> 
</head>

<body>
    <main class="register-page">
        <div class="register-box">
            <h2>Register</h2>
            <form id="register-form" method="POST" action="">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <label for="re-password">Re-enter Password</label>
                <input type="password" name="re-password" id="re-password" required>

                <label>Gender</label>
                <div class="gender-row">
                    <input type="radio" name="gender" id="male" value="male" required>
                    <label for="male">Male</label>

                    <input type="radio" name="gender" id="female" value="female" required>
                    <label for="female">Female</label>
                </div>

                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob" placeholder="dd/mm/yyyy" required>

                <div id="error-register"></div>

                <div id="error-login">
                    <?php
                        if (!empty($errors)) {
                            echo "<p style='color:red;margin-bottom:10px;'>" . $errors[0] . "</p>";
                        }
                    ?>
                </div>

                <button type="submit" class="register-btn">Register</button>

                <p class="login-text">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </form>
        </div>
    </main>
</body>
<?php include "includes/footer.php"; ?>
</html>