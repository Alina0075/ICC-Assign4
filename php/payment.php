<?php
include 'db.php';
include 'header.php';   // starts session + defines BASE_URL

// 🔴 FIX: use BASE_URL, not hardcoded URL
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔴 FIX: basic sanitization
    $order_id = (int) $_POST['order_id'];
    $amount   = (float) $_POST['amount'];

    // Insert payment (simulation)
    $conn->query("
        INSERT INTO payments (order_id, amount, payment_method, payment_status)
        VALUES ($order_id, $amount, 'simulation', 'success')
    ");

    // Update order status
    $conn->query("
        UPDATE orders 
        SET status = 'completed' 
        WHERE order_id = $order_id
    ");

    echo "
    <div style='max-width:400px;margin:50px auto;padding:20px;background:#fff;border-radius:12px;
         box-shadow:0 6px 18px rgba(0,0,0,0.08);text-align:center;'>
        <h2 style='color:#2a2a72'>Payment Successful!</h2>
        <p>Your order #$order_id is completed.</p>
        <a href='" . BASE_URL . "/home.php'>
            <button style='padding:12px 20px;background:#2a2a72;color:white;border:none;border-radius:8px;cursor:pointer;'>
                Back to Home
            </button>
        </a>
    </div>";
}
?>
