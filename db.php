<?php
// db.php
// Database connection file
 
$servername = "localhost";  // Assuming local or shared hosting
$username = "um4u5gpwc3dwc";
$password = "neqhgxo10ioe";
$dbname = "dbkgf81jnvphew";
 
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
 
// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
