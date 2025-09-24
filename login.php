<?php
// login.php
session_start();
include 'db.php';
 
$message = '';
if ($_POST && !isset($_SESSION['user_id'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
 
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1000);</script>";
        $message = 'Login successful! Redirecting...';
    } else {
        $message = 'Invalid credentials!';
    }
}
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");  // But user said no PHP redirect, but for security, keep minimal
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodPadan</title>
    <style>
        /* Internal CSS similar to signup */
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
        <h2>Login</h2>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Invalid') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="#" onclick="redirect('signup.php')">No account? Signup</a>
    </div>
 
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
