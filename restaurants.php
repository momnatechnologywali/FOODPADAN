<?php
// restaurants.php
session_start();
include 'db.php';
 
$cuisine = $_GET['cuisine'] ?? '';
$rating = $_GET['rating'] ?? '';
$location = $_GET['location'] ?? '';
 
$sql = "SELECT * FROM restaurants WHERE 1=1";
$params = [];
if ($cuisine) { $sql .= " AND cuisine = ?"; $params[] = $cuisine; }
if ($rating) { $sql .= " AND rating >= ?"; $params[] = $rating; }
if ($location) { $sql .= " AND location LIKE ?"; $params[] = "%$location%"; }
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$restaurants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - FoodPadan</title>
    <style>
        /* Internal CSS for Restaurants List - Grid Layout */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        header { background: white; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 1.5rem; color: #ff6b6b; text-decoration: none; }
        .filters { display: flex; gap: 1rem; flex-wrap: wrap; margin: 1rem 0; }
        .filters select, .filters input { padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 0.5rem 1rem; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .section { padding: 2rem; max-width: 1200px; margin: 0 auto; }
        h2 { margin-bottom: 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); transition: box-shadow 0.3s; }
        .card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .card img { width: 100%; height: 180px; object-fit: cover; }
        .card-content { padding: 1rem; }
        .card h3 { margin-bottom: 0.5rem; }
        .rating { color: #ffd700; }
        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">FoodPadan</a>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a> | <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
 
    <section class="section">
        <h2>Restaurants</h2>
        <div class="filters">
            <select onchange="filterRestaurants()">
                <option value="">All Cuisines</option>
                <option value="Italian">Italian</option>
                <option value="American">American</option>
            </select>
            <input type="number" placeholder="Min Rating" min="0" max="5" onchange="filterRestaurants()">
            <input type="text" placeholder="Location" onchange="filterRestaurants()">
            <button class="btn" onclick="redirect('index.php')">Home</button>
        </div>
        <div class="grid">
            <?php foreach ($restaurants as $rest): ?>
                <a href="restaurant.php?id=<?php echo $rest['id']; ?>" class="card">
                    <img src="<?php echo $rest['image_url'] ?? 'https://via.placeholder.com/300x180'; ?>" alt="<?php echo $rest['name']; ?>">
                    <div class="card-content">
                        <h3><?php echo $rest['name']; ?></h3>
                        <p><?php echo $rest['cuisine']; ?> • <?php echo $rest['location']; ?></p>
                        <span class="rating"><?php echo $rest['rating']; ?> ★</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
 
    <script>
        // Client-side filtering for demo (server-side already applied)
        function filterRestaurants() {
            // Reload with params
            let params = new URLSearchParams();
            const cuisine = document.querySelector('select').value;
            const rating = document.querySelector('input[type="number"]').value;
            const location = document.querySelector('input[type="text"]').value;
            if (cuisine) params.append('cuisine', cuisine);
            if (rating) params.append('rating', rating);
            if (location) params.append('location', location);
            window.location.href = 'restaurants.php?' + params.toString();
        }
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
