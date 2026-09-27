<?php
session_start();
include "includes/connect.php";
include "includes/header.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

$profileErrors = [];
$passwordErrors = [];
$deleteErrors = [];
$successMessage = "";

$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$resultUser = $stmt->get_result();
$user = $resultUser->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['update_profile'])) {

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $gender   = $_POST['gender'] ?? '';
        $dob      = $_POST['dob'] ?? '';

        if ($username === '') {
            $profileErrors[] = "Username must be filled.";
        } elseif (strlen($username) < 4 || strlen($username) > 20) {
            $profileErrors[] = "Username must be between 4 and 20 characters.";
        } elseif ($email === '') {
            $profileErrors[] = "Email must be filled.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $profileErrors[] = "Invalid email format.";
        } elseif (!str_ends_with($email, "@gmail.com")) {
            $profileErrors[] = "Email must end with '@gmail.com'.";
        } elseif ($gender === '') {
            $profileErrors[] = "Please select your gender.";
        } elseif ($dob === '') {
            $profileErrors[] = "Date of Birth must be filled.";
        } else {
            $birthDate = strtotime($dob);
            $today = strtotime(date("Y-m-d"));
            if ($birthDate >= $today) {
                $profileErrors[] = "Date of Birth must be in the past.";
            }
        }

        if (empty($profileErrors)) {
            $checkUsername = $conn->prepare(
                "SELECT userID FROM users WHERE username = ? AND userID != ?"
            );
            $checkUsername->bind_param("si", $username, $userID);
            $checkUsername->execute();
            $checkUsername->store_result();

            if ($checkUsername->num_rows > 0) {
                $profileErrors[] = "Username is already taken.";
            }
            $checkUsername->close();
        }

        if (empty($profileErrors)) {
            $stmt = $conn->prepare(
                "UPDATE users SET username=?, email=?, gender=?, dob=? WHERE userID=?"
            );
            $stmt->bind_param("ssssi", $username, $email, $gender, $dob, $userID);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $successMessage = "Profile updated successfully!";
                $user['username'] = $username;
                $user['email'] = $email;
                $user['gender'] = $gender;
                $user['dob'] = $dob;
            } else {
                $profileErrors[] = "Failed to update profile.";
            }
            $stmt->close();
        }
    }

    if (isset($_POST['update_password'])) {

        $currentPass = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPass, $user['password'])) {
            $passwordErrors[] = "Current password is incorrect.";
        } elseif (strlen($newPass) < 8) {
            $passwordErrors[] = "New password must be at least 8 characters.";
        } elseif ($newPass !== $confirmPass) {
            $passwordErrors[] = "New passwords do not match.";
        }

        if (empty($passwordErrors)) {
            $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE userID=?");
            $stmt->bind_param("si", $hashedPass, $userID);

            if ($stmt->execute()) {
                $successMessage = "Password updated successfully!";
            } else {
                $passwordErrors[] = "Failed to update password.";
            }
            $stmt->close();
        }
    }

    if (isset($_POST['delete_account'])) {

        $confirmPass = $_POST['delete_confirm_password'] ?? '';

        if (!password_verify($confirmPass, $user['password'])) {
            $deleteErrors[] = "Password incorrect. Account deletion failed.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE userID=?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->close();

            session_destroy();
            header("Location: login.php");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile - Furniland</title>
    <link rel="stylesheet" href="assets/style.css?v=3">
</head>
<body>
    <main class="profile-page">
        <div class="home-title" style="margin-bottom: 30px;">
            <h1>Your Profile</h1>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: #00c853; margin-bottom: 20px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <p style="color: red; margin-bottom: 20px;"><?php echo $errors[0]; ?></p>
        <?php endif; ?>

        <div class="profile-section">
            <h3>Update Profile Information</h3>
            <form method="POST" action="">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label>Gender</label>
                <div class="gender-row" style="margin-top: 10px; margin-bottom: 15px;">
                    <input type="radio" name="gender" value="Male" <?php echo ($user['gender'] == 'Male') ? 'checked' : ''; ?>> Male
                    <input type="radio" name="gender" value="Female" <?php echo ($user['gender'] == 'Female') ? 'checked' : ''; ?> style="margin-left: 20px;"> Female
                </div>

                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo $user['dob']; ?>" required>

                <?php if (!empty($profileErrors)): ?>
                    <p class="form-error" style="color:red; text-align:center; margin-top:3px;"><?= $profileErrors[0] ?></p>
                <?php endif; ?>

                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            </form>
        </div>

        <div class="profile-section">
            <h3>Update Password</h3>
            <form method="POST" action="">
                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>

                <?php if (!empty($passwordErrors)): ?>
                    <p class="form-error" style="color:red; text-align:center; margin-top:3px;"><?= $passwordErrors[0] ?></p>
                <?php endif; ?>

                <button type="submit" name="update_password" class="btn-save">Update Password</button>
            </form>
        </div>

        <div class="profile-section" style="border-color: #d32f2f;">
            <h3 style="color: #ff5252; border-color: #d32f2f;">Delete Account</h3>
            <p class="delete-warning">Once deleted, your account cannot be recovered.</p>
            <form method="POST" action="">
                <label>Confirm Password</label>
                <input type="password" name="delete_confirm_password" required>

                <?php if (!empty($deleteErrors)): ?>
                    <p class="form-error" style="color:red; text-align:center; margin-top:3px;"><?= $deleteErrors[0] ?></p>
                <?php endif; ?>

                <button type="submit" name="delete_account" class="btn-delete-account">Delete Account</button>
            </form>
        </div>
    </main>
    <?php include "includes/footer.php"; ?>
</body>
</html>