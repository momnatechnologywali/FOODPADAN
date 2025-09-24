<?php
// signup.php
include 'db.php';
 
$message = '';
if ($_POST) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = hashPassword($_POST['password']);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
 
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $password, $full_name, $phone, $address])) {
        $message = 'Signup successful! Redirecting to login...';
        echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
    } else {
        $message = 'Error: ' . implode(', ', $pdo->errorInfo());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - FoodPadan</title>
    <style>
        /* Internal CSS for Signup - Clean, Modern Form */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-container { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        input:focus { outline: none; border-color: #ff6b6b; }
        .btn { width: 100%; padding: 0.75rem; background: #ff6b6b; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background 0.3s; }
        .btn:hover { background: #ff5252; }
        .message { text-align: center; margin-top: 1rem; padding: 0.5rem; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        a { color: #ff6b6b; text-decoration: none; text-align: center; display: block; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Signup</h2>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" style="width:100%;"></textarea>
            </div>
            <button type="submit" class="btn">Signup</button>
        </form>
        <a href="#" onclick="redirect('login.php')">Already have an account? Login</a>
    </div>
 
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
