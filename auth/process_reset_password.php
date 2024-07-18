<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?');
            $stmt->execute([$hashed_password, $user['id']]);

            $_SESSION['message'] = "Password berhasil direset!";
            header('Location: login.php');
        } else {
            $_SESSION['error'] = "Token tidak valid atau telah kedaluwarsa.";
            header('Location: lupa_password.php?token=' . $token);
        }
    } else {
        $_SESSION['error'] = "Password tidak cocok.";
        header('Location: lupa_password.php?token=' . $token);
    }
}
?>
