<?php
// restaurant.php
session_start();
include 'db.php';
 
$rest_id = $_GET['id'] ?? 0;
if (!$rest_id) { echo "<script>window.location.href = 'restaurants.php';</script>"; exit; }
 
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$rest_id]);
$restaurant = $stmt->fetch();
 
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? AND is_available = TRUE");
$stmt->execute([$rest_id]);
$menu_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $restaurant['name']; ?> - FoodPadan</title>
    <style>
        /* Internal CSS for Restaurant Menu */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-size: 1.5rem; color: #ff6b6b; text-decoration: none; }
        .section { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .rest-header { text-align: center; margin-bottom: 2rem; }
        .rest-header img { width: 100%; max-width: 600px; height: 250px; object-fit: cover; border-radius: 10px; }
        .rest-info h1 { margin: 1rem 0; color: #333; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .menu-card { background: white; border-radius: 10px; padding: 1rem; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .menu-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; }
        .menu-card h3 { margin: 0.5rem 0; }
        .price { font-weight: bold; color: #ff6b6b; font-size: 1.2rem; }
        .btn { background: #ff6b6b; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem; }
        .btn:hover { background: #ff5252; }
        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">FoodPadan</a>
            <a href="cart.php">Cart</a>
        </nav>
    </header>
 
    <section class="section">
        <div class="rest-header">
            <img src="<?php echo $restaurant['image_url'] ?? 'https://via.placeholder.com/600x250'; ?>" alt="<?php echo $restaurant['name']; ?>">
            <div class="rest-info">
                <h1><?php echo $restaurant['name']; ?></h1>
                <p><?php echo $restaurant['description']; ?></p>
                <p><?php echo $restaurant['cuisine']; ?> • Rating: <?php echo $restaurant['rating']; ?> ★</p>
            </div>
        </div>
        <h2>Menu</h2>
        <div class="menu-grid">
            <?php foreach ($menu_items as $item): ?>
                <div class="menu-card">
                    <img src="<?php echo $item['image_url'] ?? 'https://via.placeholder.com/250x150'; ?>" alt="<?php echo $item['name']; ?>">
                    <h3><?php echo $item['name']; ?></h3>
                    <p><?php echo $item['description']; ?></p>
                    <div class="price">$<?php echo $item['price']; ?></div>
                    <button class="btn" onclick="addToCart(<?php echo $item['id']; ?>)">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
 
    <script>
        function addToCart(itemId) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existing = cart.find(item => item.id === itemId);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({id: itemId, qty: 1});
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            alert('Added to cart!');
        }
    </script>
</body>
</html>
