<?php
// track.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) { echo "<script>window.location.href = 'orders.php';</script>"; exit; }
 
$stmt = $pdo->prepare("SELECT o.*, r.name as rest_name FROM orders o JOIN restaurants r ON o.restaurant_id = r.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();
 
// Update status for demo (in real, from rider app)
if (isset($_GET['update_status'])) {
    $new_status = $_GET['update_status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    $order['status'] = $new_status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - FoodPadan</title>
    <style>
        /* Internal CSS for Tracking - Progress Bar */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .track-container { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center; max-width: 400px; }
        h2 { margin-bottom: 1rem; }
        .status { font-size: 1.2rem; margin: 1rem 0; color: #4caf50; }
        .progress { display: flex; justify-content: space-between; margin: 2rem 0; }
        .step { flex: 1; text-align: center; position: relative; }
        .step:not(:last-child)::after { content: ''; position: absolute; top: 20px; left: 50%; width: 100px; height: 2px; background: #ddd; transform: translateX(50%); z-index: -1; }
        .step.active { color: #ff6b6b; }
        .step.active .circle { background: #ff6b6b; color: white; }
        .circle { width: 40px; height: 40px; border-radius: 50%; background: #ddd; line-height: 40px; margin: 0 auto 0.5rem; font-weight: bold; }
        .details { text-align: left; margin-top: 2rem; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
        .btn { padding: 0.5rem 1rem; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="track-container">
        <h2>Order #<?php echo $order_id; ?></h2>
        <p class="status"><?php echo $order['status']; ?></p>
        <div class="progress">
            <div class="step <?php echo in_array($order['status'], ['Processing', 'On the Way']) ? 'active' : ''; ?>">
                <div class="circle">1</div>
                <div>Processing</div>
            </div>
            <div class="step <?php echo $order['status'] === 'On the Way' || $order['status'] === 'Delivered' ? 'active' : ''; ?>">
                <div class="circle">2</div>
                <div>On the Way</div>
            </div>
            <div class="step <?php echo $order['status'] === 'Delivered' ? 'active' : ''; ?>">
                <div class="circle">3</div>
                <div>Delivered</div>
            </div>
        </div>
        <div class="details">
            <div class="detail-row"><span>Restaurant:</span><span><?php echo $order['rest_name']; ?></span></div>
            <div class="detail-row"><span>Total:</span><span>$<?php echo $order['total_amount']; ?></span></div>
            <div class="detail-row"><span>Payment:</span><span><?php echo $order['payment_method']; ?></span></div>
            <div class="detail-row"><span>Address:</span><span><?php echo $order['delivery_address']; ?></span></div>
        </div>
        <button class="btn" onclick="redirect('orders.php')">View Orders</button>
    </div>
 
    <script>
        // Simulate real-time update (poll or websocket in real)
        setInterval(() => {
            // Demo: random update
            const statuses = ['Processing', 'On the Way', 'Delivered'];
            const current = '<?php echo $order['status']; ?>';
            const nextIndex = (statuses.indexOf(current) + 1) % statuses.length;
            if (Math.random() > 0.7) {  // 30% chance
                window.location.href = `track.php?order_id=<?php echo $order_id; ?>&update_status=${statuses[nextIndex]}`;
            }
        }, 5000);
 
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
