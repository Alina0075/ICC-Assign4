<?php
// db.php - Database connection

$servername = "localhost";
$username = "root";           
$password = "AlinaAdmin@2026";  
$dbname = "readsmart_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Stop execution if connection fails
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset to avoid UTF-8 issues
$conn->set_charset("utf8mb4");
?>
