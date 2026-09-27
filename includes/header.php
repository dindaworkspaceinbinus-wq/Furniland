<?php
$isLoggedIn = isset($_SESSION['userID']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>

<link rel="stylesheet" href="assets/style.css?v=4">

<header>
    <div class="nav-left">
        <a href="home.php" class="logo">Furniland</a>
        <nav>
            <ul class="nav-links">
                <?php if (!$isLoggedIn): ?>
                    <li><a href="home.php">Home</a></li>
                
                <?php elseif ($role === 'member'): ?>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="product_catalog.php">Catalog</a></li>
                
                <?php elseif ($role === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <div class="nav-right">
    <?php if ($isLoggedIn): ?>
        
        <?php if ($role === 'member'): ?>
            <a href="profile.php" class="user-greeting">Hello, <?php echo htmlspecialchars($username); ?> </a>
            <a href="cart.php" class="nav-links">Cart</a>
            <a href="history.php" class="nav-links">History</a>
        
        <?php elseif ($role === 'admin'): ?>
            <span class="user-greeting">Hello, Admin <?php echo htmlspecialchars($username); ?></span>
        
        <?php endif; ?>
        
        <a href="logout.php" class="btn-logout">Logout</a>

    <?php else: ?>
        <a href="login.php" class="btn-login">Login</a>
    <?php endif; ?>
</div>
</header>