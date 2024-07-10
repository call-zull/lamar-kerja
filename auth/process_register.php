<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $approved = 0; // Default approval status is 0 (not approved)
    $no_telp = $_POST['no_telp']; // All roles require WhatsApp number

    if ($role === 'mahasiswa') {
        $nim = $_POST['nim'];
        $nama_mahasiswa = $_POST['nama_mahasiswa'];
        $jurusan_id = $_POST['jurusan_id'];
        $prodi_id = $_POST['prodi_id'];

        // Validate NIM format
        if (!preg_match('/^[A-Ea-e]\d{9}$/', $nim)) {
            $_SESSION['error'] = 'NIM tidak valid. Format yang benar adalah huruf A-E atau a-e diikuti oleh 9 angka.';
            header('Location: register.php');
            exit;
        }

        // Validate Jurusan and Program Studi
        $stmtJurusan = $pdo->prepare("SELECT COUNT(*) FROM jurusans WHERE id = ?");
        $stmtProdi = $pdo->prepare("SELECT COUNT(*) FROM prodis WHERE id = ?");
        if (!$stmtJurusan->execute([$jurusan_id]) || !$stmtProdi->execute([$prodi_id]) || $stmtJurusan->fetchColumn() == 0 || $stmtProdi->fetchColumn() == 0) {
            $_SESSION['error'] = 'Jurusan atau Program Studi tidak valid.';
            header('Location: register.php');
            exit;
        }
    }

    try {
        $pdo->beginTransaction();

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username sudah ada.';
            header('Location: register.php');
            exit;
        }

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, approved, no_telp) VALUES (:username, :password, :role, :approved, :no_telp)");
        $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'role' => $role, 'approved' => $approved, 'no_telp' => $no_telp]);
        $userId = $pdo->lastInsertId();

        if ($role === 'mahasiswa') {
            // Insert mahasiswa data
            $stmtMahasiswa = $pdo->prepare("INSERT INTO mahasiswas (user_id, nama_mahasiswa, nim, jurusan_id, prodi_id) VALUES (:user_id, :nama_mahasiswa, :nim, :jurusan_id, :prodi_id)");
            $stmtMahasiswa->execute([
                'user_id' => $userId,
                'nama_mahasiswa' => $nama_mahasiswa,
                'nim' => $nim,
                'jurusan_id' => $jurusan_id,
                'prodi_id' => $prodi_id
            ]);
            $_SESSION['success'] = 'Registrasi berhasil! Mohon tunggu persetujuan admin.';
        } elseif ($role === 'perusahaan') {
            // Insert perusahaan data
            $stmtPerusahaan = $pdo->prepare("INSERT INTO perusahaans (user_id, approved, no_telp) VALUES (:user_id, 0, :no_telp)");
            $stmtPerusahaan->execute(['user_id' => $userId, 'no_telp' => $no_telp]);
            $_SESSION['success'] = 'Registrasi berhasil. Mohon tunggu admin mengkonfirmasi akun.';
        }

        $pdo->commit();
        header('Location: register.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Gagal melakukan registrasi: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }

} else {
    header('Location: register.php');
    exit;
}
?>
