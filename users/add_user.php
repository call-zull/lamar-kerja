<?php
// Include the database connection file
require 'db.php';

// Data pengguna baru
$username = 'admin';
$password_plain = 'admin';
$role = 'admin';

// Hash password
$hashedPassword = password_hash($password_plain, PASSWORD_DEFAULT);

// Tambahkan pengguna ke database
try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':role' => $role
    ]);
    echo "User added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
