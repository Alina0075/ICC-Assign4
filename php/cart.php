<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode(BASE_URL . "/cart.php"));
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items (SECURE)
$stmt = $conn->prepare("
    SELECT c.cart_id, b.title, b.price, c.quantity 
    FROM cart c 
    JOIN books b ON c.book_id = b.book_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart - ReadSmart</title>
<style>
body { font-family:'Roboto', sans-serif; background:#f9f9f9; margin:0; padding:0; }
.container { max-width:900px; margin:50px auto; }
h2 { text-align:center; color:#2a2a72; margin-bottom:30px; }
.cart-item {
    background:#fff;
    padding:20px;
    margin-bottom:15px;
    border-radius:12px;
    display:flex;
    justify-content:space-between;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}
.total {
    text-align:right;
    font-size:20px;
    margin-top:20px;
    font-weight:600;
}
.checkout-btn {
    padding:12px 25px;
    background:#2a2a72;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-top:15px;
    float:right;
}
.checkout-btn:hover { background:#1f1f5c; }
.empty {
    text-align:center;
    font-size:18px;
    color:#555;
    padding:40px;
}
</style>
</head>

<body>
<div class="container">
    <h2>Your Cart</h2>

    <?php if ($result->num_rows === 0): ?>

        <div class="empty">
            Your cart is empty 🛒<br><br>
            <a href="<?= BASE_URL ?>/index.html">Continue Shopping</a>
        </div>

    <?php else: ?>

        <?php while ($row = $result->fetch_assoc()):
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
        ?>
            <div class="cart-item">
                <span><?= htmlspecialchars($row['title']) ?> × <?= $row['quantity'] ?></span>
                <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
        <?php endwhile; ?>

        <div class="total">Total: $<?= number_format($total, 2) ?></div>

        <form method="post" action="checkout.php">
            <input type="hidden" name="total" value="<?= $total ?>">
            <button type="submit" class="checkout-btn">Checkout</button>
        </form>

    <?php endif; ?>

</div>
</body>
</html>
