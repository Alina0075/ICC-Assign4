<?php
include 'header.php';

if(!isset($_SESSION['user_id'])) {
    $login_redirect = BASE_URL . "/login.php?redirect=" . urlencode(BASE_URL . "/cart.php");
}

// Fetch books from DB
$books_result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ReadSmart Books</title>
<style>
body { font-family:'Roboto', sans-serif; background:#f9f9f9; margin:0; }
.cards { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; padding:50px 20px; }
.card { background:#fff; width:260px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1); overflow:hidden; display:flex; flex-direction:column; }
.card img { width:100%; height:300px; object-fit:cover; }
.card-content { padding:15px; flex:1; display:flex; flex-direction:column; }
.card h3 { margin-bottom:10px; color:#2a2a72; }
.card p { margin-bottom:10px; font-weight:500; }
.card form { display:flex; flex-direction:column; margin-top:auto; }
.card form input[type="number"] { width:80px; padding:6px; margin-bottom:12px; border-radius:6px; border:1px solid #ccc; }
.card form button { background:#2a2a72; color:#fff; border:none; border-radius:8px; padding:12px; cursor:pointer; transition:0.3s; }
.card form button:hover { background:#1f1f5c; }
h1.section-title { text-align:center; color:#2a2a72; font-size:36px; margin-top:40px; }
</style>
</head>
<body>

<h1 class="section-title">Featured Books</h1>
<div class="cards">
<?php while($book = $books_result->fetch_assoc()): ?>
    <div class="card">
        <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>">
        <div class="card-content">
            <h3><?php echo $book['title']; ?></h3>
            <p>$<?php echo $book['price']; ?></p>
            <form method="post" action="<?php echo BASE_URL; ?>/add_to_cart.php">
                <input type="number" name="quantity" value="1" min="1">
                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                <input type="hidden" name="redirect" value="<?php echo BASE_URL; ?>/cart.php">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    </div>
<?php endwhile; ?>
</div>
</body>
</html>
