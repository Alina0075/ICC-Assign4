<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH CART FROM DB
   ========================= */
$stmt = $conn->prepare("
    SELECT c.book_id, b.price, c.quantity
    FROM cart c
    JOIN books b ON c.book_id = b.book_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: cart.php");
    exit;
}

/* =========================
   CALCULATE TOTAL
   ========================= */
$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}

/* =========================
   CREATE ORDER
   ========================= */
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, total_amount)
    VALUES (?, ?)
");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();

$order_id = $stmt->insert_id;

/* =========================
   SAVE ORDER ITEMS
   ========================= */
$stmt = $conn->prepare("
    INSERT INTO order_items (order_id, book_id, quantity, price)
    VALUES (?, ?, ?, ?)
");

foreach ($items as $item) {
    $stmt->bind_param(
        "iiid",
        $order_id,
        $item['book_id'],
        $item['quantity'],
        $item['price']
    );
    $stmt->execute();
}

/* =========================
   CLEAR CART
   ========================= */
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Placed - ReadSmart</title>
<style>
body {
    font-family:'Roboto', sans-serif;
    background:#f9f9f9;
}
.container {
    max-width:600px;
    margin:60px auto;
    background:#fff;
    padding:40px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    text-align:center;
}
h2 {
    color:#2a2a72;
}
a {
    display:inline-block;
    margin-top:20px;
    padding:12px 22px;
    background:#2a2a72;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
}
a:hover {
    background:#1f1f5c;
}
</style>
</head>

<body>
<div class="container">
    <h2>Order Placed Successfully 🎉</h2>
    <p>Your total amount is <strong>$<?= number_format($total, 2) ?></strong></p>
    <a href="index.html">Continue Shopping</a>
</div>
</body>
</html>
