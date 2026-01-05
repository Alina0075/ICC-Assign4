<?php
include 'db.php';

/* SAFETY: define BASE_URL if header.php did not */
if (!defined('BASE_URL')) {
    define("BASE_URL", "http://readsmart-alb-1880170115.us-east-1.elb.amazonaws.com");
}

include 'header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare(
        "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $message = "User registered successfully! 
        <a href='" . BASE_URL . "/login.php'>Login here</a>";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<div style="max-width:400px;margin:50px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
    <h2 style="text-align:center;color:#2a2a72;margin-bottom:20px;">Sign Up</h2>

    <?php 
    if ($message) {
        echo "<p style='text-align:center;color:green;'>$message</p>";
    }
    ?>

    <form method="post" action="<?php echo BASE_URL; ?>/signup.php">
        <label>Username</label><br>
        <input type="text" name="username" required style="width:100%;padding:10px;margin-bottom:15px;"><br>

        <label>Email</label><br>
        <input type="email" name="email" required style="width:100%;padding:10px;margin-bottom:15px;"><br>

        <label>Password</label><br>
        <input type="password" name="password" required style="width:100%;padding:10px;margin-bottom:15px;"><br>

        <button type="submit" style="padding:12px 20px;width:100%;background:#2a2a72;color:white;border:none;border-radius:8px;cursor:pointer;">
            Sign Up
        </button>
    </form>
</div>
