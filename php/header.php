<?php
// Start session
session_start();

// Include database connection
include 'db.php';

// Define BASE_URL if not defined
if (!defined('BASE_URL')) {
    define("BASE_URL", "http://readsmart-alb-1880170115.us-east-1.elb.amazonaws.com");
}
?>
<header style="display:flex;justify-content:space-between;align-items:center;padding:20px 60px;background:#fff;box-shadow:0 4px 15px rgba(0,0,0,0.08);position:sticky;top:0;z-index:100;">
    <span style="font-family:'Playfair Display', serif;font-size:28px;color:#2a2a72;">ReadSmart</span>
    <nav>
        <a href="<?php echo BASE_URL; ?>/index.php" style="margin-left:35px;font-weight:500;">Home</a>
        <a href="<?php echo BASE_URL; ?>/products.php" style="margin-left:35px;font-weight:500;">Books</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/cart.php" style="margin-left:35px;font-weight:500;">Cart</a>
            <a href="<?php echo BASE_URL; ?>/logout.php" style="margin-left:35px;font-weight:500;">Logout</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/signup.php" style="margin-left:35px;font-weight:500;">Sign Up</a>
            <a href="<?php echo BASE_URL; ?>/login.php" style="margin-left:35px;font-weight:500;">Login</a>
        <?php endif; ?>
    </nav>
</header>
