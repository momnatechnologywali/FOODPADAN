<?php
// orders.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT o.*, r.name as rest_name FROM orders o JOIN restaurants r ON o.restaurant_id = r.id WHERE o.user_id = ? ORDER BY o.order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - FoodPadan</title>
    <style>
        /* Internal CSS for Orders List */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-size: 1.5rem; color: #ff6b6b; text-decoration: none; }
        .section { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .order-card { background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .status { padding: 0.25rem 0.5rem; border-radius: 15px; color: white; font-weight: bold; }
        .processing { background: #ff9800; }
        .on-way { background: #2196f3; }
        .delivered { background: #4caf50; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.9rem; }
        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">FoodPadan</a>
            <a href="profile.php">Profile</a>
        </nav>
    </header>
 
    <section class="section">
        <h2>My Orders</h2>
        <?php foreach ($orders as $order): ?>
            <a href="track.php?order_id=<?php echo $order['id']; ?>" class="order-card">
                <div class="order-header">
                    <h3>Order #<?php echo $order['id']; ?></h3>
                    <span class="status <?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>"><?php echo $order['status']; ?></span>
                </div>
                <div class="detail-row"><span>Restaurant:</span><span><?php echo $order['rest_name']; ?></span></div>
                <div class="detail-row"><span>Total:</span><span>$<?php echo $order['total_amount']; ?></span></div>
                <div class="detail-row"><span>Date:</span><span><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></span></div>
            </a>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
            <p>No orders yet. <a href="restaurants.php">Browse restaurants</a></p>
        <?php endif; ?>
    </section>
</body>
</html>
