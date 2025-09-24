<?php
// index.php
session_start();
include 'db.php';
 
// Fetch featured restaurants
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE is_featured = TRUE LIMIT 6");
$stmt->execute();
$featured_restaurants = $stmt->fetchAll();
 
// Fetch trending dishes (example: high-order items, simplified for demo)
$stmt = $pdo->prepare("SELECT mi.*, r.name as rest_name FROM menu_items mi JOIN restaurants r ON mi.restaurant_id = r.id WHERE mi.is_available = TRUE LIMIT 6");
$stmt->execute();
$trending_dishes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodPadan - Homepage</title>
    <style>
        /* Internal CSS for Homepage - Modern, Responsive, and Visually Appealing */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #333; line-height: 1.6; }
        header { background: rgba(255,255,255,0.95); padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff6b6b; text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 2rem; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff6b6b; }
        .auth-btns { display: flex; gap: 1rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 25px; cursor: pointer; transition: all 0.3s; font-weight: 500; }
        .btn-primary { background: #ff6b6b; color: white; }
        .btn-primary:hover { background: #ff5252; transform: translateY(-2px); }
        .hero { background: url('https://via.placeholder.com/1920x600?text=Delicious+Food') center/cover; height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; color: white; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; }
        .search-bar { display: flex; max-width: 500px; margin: 0 auto; background: white; border-radius: 50px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .search-bar input { flex: 1; padding: 1rem; border: none; font-size: 1rem; }
        .search-bar button { padding: 1rem 2rem; background: #ff6b6b; color: white; border: none; cursor: pointer; transition: background 0.3s; }
        .search-bar button:hover { background: #ff5252; }
        .section { padding: 4rem 2rem; max-width: 1200px; margin: 0 auto; }
        .section h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.2); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 1.5rem; }
        .card h3 { font-size: 1.3rem; margin-bottom: 0.5rem; color: #333; }
        .card p { color: #666; margin-bottom: 1rem; }
        .rating { color: #ffd700; font-weight: bold; }
        .price { color: #ff6b6b; font-weight: bold; }
        .offers { background: rgba(255,255,255,0.1); border-radius: 15px; padding: 2rem; text-align: center; color: white; }
        .offers p { font-size: 1.2rem; }
        footer { background: #333; color: white; text-align: center; padding: 2rem; }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1rem; }
            .grid { grid-template-columns: 1fr; }
            .section { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo">FoodPadan</a>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#" onclick="redirect('restaurants.php')">Restaurants</a></li>
                <li><a href="#" onclick="redirect('orders.php')">Orders</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="#" onclick="redirect('profile.php')">Profile</a></li>
                    <li><a href="#" onclick="redirect('logout.php')">Logout</a></li>
                <?php endif; ?>
            </ul>
            <div class="auth-btns">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <button class="btn" onclick="redirect('login.php')">Login</button>
                    <button class="btn btn-primary" onclick="redirect('signup.php')">Signup</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>
 
    <section class="hero">
        <div>
            <h1>Order Delicious Food Online</h1>
            <p>Discover amazing restaurants and cuisines near you</p>
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Search for restaurants or dishes...">
                <button onclick="search()">Search</button>
            </div>
        </div>
    </section>
 
    <section class="section">
        <h2>Featured Restaurants</h2>
        <div class="grid" id="featured-restaurants">
            <?php foreach ($featured_restaurants as $rest): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($rest['image_url'] ?? 'https://via.placeholder.com/300x200'); ?>" alt="<?php echo htmlspecialchars($rest['name']); ?>">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($rest['name']); ?></h3>
                        <p><?php echo htmlspecialchars($rest['description']); ?></p>
                        <span class="rating"><?php echo number_format($rest['rating'], 1); ?> ★</span>
                        <button class="btn btn-primary" onclick="redirect('restaurant.php?id=<?php echo $rest['id']; ?>')">View Menu</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
 
    <section class="section">
        <h2>Trending Dishes</h2>
        <div class="grid" id="trending-dishes">
            <?php foreach ($trending_dishes as $dish): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($dish['image_url'] ?? 'https://via.placeholder.com/150x150'); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($dish['name']); ?> <span style="font-size: 0.9rem; color: #666;">(<?php echo htmlspecialchars($dish['rest_name']); ?>)</span></h3>
                        <p><?php echo htmlspecialchars($dish['description']); ?></p>
                        <span class="price">$<?php echo number_format($dish['price'], 2); ?></span>
                        <button class="btn btn-primary" onclick="addToCart(<?php echo $dish['id']; ?>, '<?php echo htmlspecialchars($dish['name']); ?>', <?php echo $dish['price']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
 
    <section class="section offers">
        <h2>Special Offers</h2>
        <p>20% off on your first order! Use code: <strong>WELCOME20</strong></p>
        <p>Free delivery on orders above $30!</p>
    </section>
 
    <footer>
        <p>&copy; 2025 FoodPadan. All rights reserved.</p>
    </footer>
 
    <script>
        // Internal JavaScript for Homepage
        function redirect(url) {
            window.location.href = url;
        }
 
        function search() {
            const query = document.getElementById('search-input').value;
            if (query) {
                redirect(`restaurants.php?location=${encodeURIComponent(query)}`);
            } else {
                alert('Please enter a search term');
            }
        }
 
        function addToCart(itemId, itemName, price) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existing = cart.find(item => item.id === itemId);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id: itemId, name: itemName, price: price, qty: 1 });
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            alert(`${itemName} added to cart!`);
        }
 
        // Simulate dynamic updates (e.g., new offers)
        setTimeout(() => {
            document.querySelector('.offers').innerHTML += '<p>New Offer: Buy 1 Get 1 Free on Pizzas this weekend!</p>';
        }, 5000);
    </script>
</body>
</html>
