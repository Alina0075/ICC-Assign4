<?php
include 'header.php';

$book_id  = $_POST['book_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

$redirect = BASE_URL . "/cart.php";

if ($book_id <= 0 || $quantity <= 0) {
    header("Location: $redirect");
    exit;
}

/* =========================
   USER NOT LOGGED IN
   ========================= */
if (!isset($_SESSION['user_id'])) {

    // Initialize session cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add / update session cart
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id] += $quantity;
    } else {
        $_SESSION['cart'][$book_id] = $quantity;
    }

    // Redirect to login AFTER saving cart
    header("Location: " . BASE_URL . "/login.php?redirect=" . urlencode($redirect));
    exit;
}

/* =========================
   USER LOGGED IN
   ========================= */

$user_id = $_SESSION['user_id'];

// Check if book already exists in DB cart
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND book_id=?");
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND book_id=?");
    $stmt->bind_param("iii", $new_quantity, $user_id, $book_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?,?,?)");
    $stmt->bind_param("iii", $user_id, $book_id, $quantity);
    $stmt->execute();
}

header("Location: $redirect");
exit;
