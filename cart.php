<?php
// cart.php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
// For demo, use localStorage cart, but in real, session or DB cart
// Here, simulate with sample
$cart_items = [];  // Fetch from DB or session
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FoodPadan</title>
    <style>
        /* Internal CSS for Cart */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; }
        .logo { font-size: 1.5rem; color: #ff6b6b; text-decoration: none; }
        .section { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .item-details { flex: 1; }
        .item-details h3 { margin-bottom: 0.5rem; }
        .quantity { display: flex; align-items: center; gap: 0.5rem; }
        button { background: #ff6b6b; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 3px; cursor: pointer; }
        .total { text-align: right; font-size: 1.2rem; font-weight: bold; margin-top: 1rem; }
        .checkout-btn { width: 100%; padding: 1rem; background: #4caf50; color: white; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer; margin-top: 1rem; }
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
        <h2>Your Cart</h2>
        <div id="cart-items">
            <!-- JS will populate -->
        </div>
        <div class="total" id="total">Total: $0.00</div>
        <button class="checkout-btn" onclick="redirect('checkout.php')">Proceed to Checkout</button>
    </section>
 
    <script>
        // Load cart from localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const container = document.getElementById('cart-items');
        let total = 0;
 
        // Sample prices (in real, fetch from DB)
        const prices = {1: 12.99, 2: 14.99, 3: 8.99};  // Map itemId to price
 
        cart.forEach(item => {
            const price = prices[item.id] || 0;
            const itemTotal = price * item.qty;
            total += itemTotal;
            container.innerHTML += `
                <div class="cart-item">
                    <div class="item-details">
                        <h3>Item ${item.id}</h3>
                        <p>$${price} x ${item.qty}</p>
                    </div>
                    <div class="quantity">
                        <button onclick="updateQty(${item.id}, -1)">-</button>
                        <span>${item.qty}</span>
                        <button onclick="updateQty(${item.id}, 1)">+</button>
                        <button onclick="removeItem(${item.id})">Remove</button>
                    </div>
                    <span>$${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
 
        document.getElementById('total').textContent = `Total: $${total.toFixed(2)}`;
 
        function updateQty(id, delta) {
            cart = cart.map(item => item.id === id ? {...item, qty: Math.max(1, item.qty + delta)} : item);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }
 
        function removeItem(id) {
            cart = cart.filter(item => item.id !== id);
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }
 
        function redirect(url) { if (total > 0) window.location.href = url; else alert('Cart empty!'); }
    </script>
</body>
</html>
