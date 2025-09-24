<?php
// profile.php
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
 
if ($_POST) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$full_name, $phone, $address, $user_id]);
    $user = array_merge($user, ['full_name' => $full_name, 'phone' => $phone, 'address' => $address]);  // Refresh
    echo "<script>alert('Profile updated!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - FoodPadan</title>
    <style>
        /* Internal CSS for Profile */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem; }
        .profile-container { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 500px; margin: 0 auto; }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { width: 100%; padding: 0.75rem; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 1rem; }
        .btn:hover { background: #ff5252; }
        a { color: #ff6b6b; text-decoration: none; display: block; text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <button type="submit" class="btn">Update Profile</button>
        </form>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
        <a href="#" onclick="logout()">Logout</a>
    </div>
 
    <script>
        function redirect(url) { window.location.href = url; }
        function logout() {
            if (confirm('Logout?')) {
                // Clear session via PHP, but for demo
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
