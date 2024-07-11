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
        // Check if the user's role is admin
        if ($user['role'] === 'admin') {
            // Admin is approved by default in this example
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: ../admin/index.php');
            exit;
        } elseif ($user['role'] === 'cdc') {
            // Query to check approved status for CDC
            $stmtCDC = $pdo->prepare("SELECT approved FROM cdcs WHERE user_id = :user_id");
            $stmtCDC->execute(['user_id' => $user['id']]);
            $cdc = $stmtCDC->fetch();

            // Check approved status
            if ($cdc && $cdc['approved'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['cdc_id'] = $user['id'];  // Assuming user ID is also CDC ID for simplicity
                header('Location: ../cdc/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                header('Location: login.php');
                exit;
            }
        } else {
            // Check if mahasiswa or perusahaan is approved
            if ($user['role'] === 'mahasiswa') {
                // Query to check approved status for mahasiswa
                $stmtMahasiswa = $pdo->prepare("SELECT approved FROM mahasiswas WHERE user_id = :user_id");
                $stmtMahasiswa->execute(['user_id' => $user['id']]);
                $mahasiswa = $stmtMahasiswa->fetch();

                // Check approved status
                if ($mahasiswa && $mahasiswa['approved'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['mahasiswa_id'] = $user['id'];  // Assuming user ID is also mahasiswa ID for simplicity
                    header('Location: ../mahasiswa/index.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                    header('Location: login.php');
                    exit;
                }
            } elseif ($user['role'] === 'perusahaan') {
                // Query to check approved status for perusahaan
                $stmtPerusahaan = $pdo->prepare("SELECT approved FROM perusahaans WHERE user_id = :user_id");
                $stmtPerusahaan->execute(['user_id' => $user['id']]);
                $perusahaan = $stmtPerusahaan->fetch();

                // Check approved status
                if ($perusahaan && $perusahaan['approved'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['perusahaan_id'] = $user['id'];  // Assuming user ID is also perusahaan ID for simplicity
                    header('Location: ../perusahaan/index.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
                    header('Location: login.php');
                    exit;
                }
            }
        }

        // If not admin, CDC, mahasiswa, or perusahaan, show error
        $_SESSION['error'] = 'Anda belum disetujui untuk masuk.';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Username atau password salah';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
