<?php
// checkout.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
 
// Process order if POST
if ($_POST) {
    $restaurant_id = $_POST['restaurant_id'];  // Assume from cart
    $delivery_address = $_POST['delivery_address'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
 
    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, restaurant_id, total_amount, delivery_address, payment_method) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $restaurant_id, $total_amount, $delivery_address, $payment_method])) {
        $order_id = $pdo->lastInsertId();
        // Add order items from cart (demo: assume one item)
        $cart = json_decode($_POST['cart_data'], true);  // Pass cart in form hidden
        foreach ($cart as $item) {
            $stmt_item = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");
            $stmt_item->execute([$item['id']]);
            $price = $stmt_item->fetchColumn();
            $stmt_oi = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_oi->execute([$order_id, $item['id'], $item['qty'], $price]);
        }
        // Clear cart
        echo "<script>localStorage.removeItem('cart'); alert('Order placed!'); window.location.href = 'track.php?order_id=$order_id';</script>";
    } else {
        echo "<script>alert('Error placing order!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FoodPadan</title>
    <style>
        /* Internal CSS for Checkout */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-size: 1.5rem; color: #ff6b6b; text-decoration: none; }
        .section { max-width: 600px; margin: 2rem auto; padding: 0 1rem; background: white; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input, select, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; }
        .payment-options { display: flex; gap: 1rem; margin: 1rem 0; }
        .payment-options input { width: auto; }
        .btn { width: 100%; padding: 1rem; background: #4caf50; color: white; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer; margin-top: 1rem; }
        .total { text-align: right; font-size: 1.2rem; font-weight: bold; padding: 1rem; border-top: 1px solid #ddd; }
        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">FoodPadan</a>
            <a href="cart.php">Back to Cart</a>
        </nav>
    </header>
 
    <section class="section">
        <h2>Checkout</h2>
        <form method="POST">
            <div class="form-group">
                <label>Delivery Address</label>
                <textarea name="delivery_address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            <div class="payment-options">
                <label><input type="radio" name="payment_method" value="COD" checked> Cash on Delivery</label>
                <label><input type="radio" name="payment_method" value="online"> Online Payment (Dummy)</label>
            </div>
            <input type="hidden" name="restaurant_id" value="1">  <!-- From cart -->
            <input type="hidden" name="total_amount" id="total" value="0">
            <input type="hidden" name="cart_data" id="cart_data" value="">
            <div class="total">Total: $<span id="total-display">0.00</span></div>
            <button type="submit" class="btn">Place Order</button>
        </form>
    </section>
 
    <script>
        // Load cart total
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const prices = {1: 12.99, 2: 14.99, 3: 8.99};
        let total = cart.reduce((sum, item) => sum + (prices[item.id] * item.qty), 0);
        document.getElementById('total').value = total;
        document.getElementById('cart_data').value = JSON.stringify(cart);
        document.getElementById('total-display').textContent = total.toFixed(2);
 
        // Dummy online payment
        document.querySelector('input[value="online"]').addEventListener('change', () => {
            if (confirm('Simulate online payment?')) alert('Payment successful!');
        });
    </script>
</body>
</html>
