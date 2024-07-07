<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to get user based on username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verify user and password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'mahasiswa') {
            $stmtMahasiswa = $pdo->prepare("SELECT id FROM mahasiswas WHERE user_id = :user_id");
            $stmtMahasiswa->execute(['user_id' => $user['id']]);
            $mahasiswa = $stmtMahasiswa->fetch();
            $_SESSION['mahasiswa_id'] = $mahasiswa['id'];
        } elseif ($user['role'] === 'cdc') {
            $stmtCDC = $pdo->prepare("SELECT id FROM cdcs WHERE user_id = :user_id");
            $stmtCDC->execute(['user_id' => $user['id']]);
            $cdc = $stmtCDC->fetch();
            $_SESSION['cdc_id'] = $cdc['id'];
        } elseif ($user['role'] === 'perusahaan') {
            $stmtPerusahaan = $pdo->prepare("SELECT id FROM perusahaans WHERE user_id = :user_id");
            $stmtPerusahaan->execute(['user_id' => $user['id']]);
            $perusahaan = $stmtPerusahaan->fetch();
            $_SESSION['perusahaan_id'] = $perusahaan['id'];
        }

        // Redirect user based on role
        switch ($user['role']) {
            case 'admin':
                header('Location: ../admin/index.php');
                break;
            case 'cdc':
                header('Location: ../cdc/index.php');
                break;
            case 'mahasiswa':
                header('Location: ../mahasiswa/index.php');
                break;
            case 'perusahaan':
                header('Location: ../perusahaan/index.php');
                break;
            default:
                $_SESSION['error'] = 'Peran pengguna tidak valid';
                header('Location: login.php');
                break;
        }
        exit;
    } else {
        $_SESSION['error'] = 'Invalid username or password';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
