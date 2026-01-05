<?php
// -----------------------------
// DEBUGGING / ERROR REPORTING
// -----------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header (starts session, DB connection, defines BASE_URL)
include 'header.php';

$message = "";

// Default redirect
$redirect = $_GET['redirect'] ?? BASE_URL . "/cart.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim inputs
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $redirect = $_POST['redirect'] ?? BASE_URL . "/cart.php";

    // Check empty input
    if (empty($email) || empty($password)) {
        $message = "Please enter email and password.";
    } else {

        // Fetch user using prepared statement
        $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email=?");
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $password_hash);
        if ($stmt->fetch()) {
            $user = [
                'user_id' => $user_id,
                'password_hash' => trim((string)$password_hash)
            ];
        } else {
            $user = null;
        }
        $stmt->close();

        // Verify password
        if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {

            // Set session
            $_SESSION['user_id'] = $user['user_id'];

            // Merge guest cart into user cart
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $book_id => $qty) {

                    $qty = max(1, intval($qty));

                    // Check if item exists
                    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND book_id=?");
                    $stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
                    $stmt->execute();
                    $stmt->bind_result($existing_qty);
                    $stmt->fetch();
                    $stmt->close();

                    if (!empty($existing_qty)) {
                        $new_qty = $existing_qty + $qty;
                        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND book_id=?");
                        $stmt->bind_param("iii", $new_qty, $_SESSION['user_id'], $book_id);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?,?,?)");
                        $stmt->bind_param("iii", $_SESSION['user_id'], $book_id, $qty);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
                unset($_SESSION['cart']);
            }

            // Redirect
            header("Location: $redirect");
            exit;

        } else {
            $message = "Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ReadSmart Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    font-family: 'Roboto', sans-serif;
    background:#f1f5f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.container {
    background:#fff;
    padding:50px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    width:360px;
    text-align:center;
}
.container h2 {
    margin-bottom:25px;
    color:#2a2a72;
    font-family:'Playfair Display', serif;
}
input {
    width:100%;
    padding:12px;
    margin:12px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:16px;
}
button {
    width:100%;
    padding:14px;
    background:#2a2a72;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}
button:hover {
    background:#1f1f5c;
}
.message {
    color:red;
    margin-bottom:15px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Login to ReadSmart</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
