<?php
session_start();
include('../includes/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php'; // Pastikan ini benar jika ada lokasi atau nama file yang berbeda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = $_POST['email'];

        // Pencarian email di tabel mahasiswas
        $stmt = $pdo->prepare('SELECT user_id FROM mahasiswas WHERE email = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        // Pencarian email di tabel cdcs jika tidak ditemukan di tabel mahasiswas
        if (!$result) {
            $stmt = $pdo->prepare('SELECT user_id FROM cdcs WHERE email_cdc = ?');
            $stmt->execute([$email]);
            $result = $stmt->fetch();
        }

        // Pencarian email di tabel perusahaans jika tidak ditemukan di tabel cdcs
        if (!$result) {
            $stmt = $pdo->prepare('SELECT user_id FROM perusahaans WHERE email_perusahaan = ?');
            $stmt->execute([$email]);
            $result = $stmt->fetch();
        }

        // Jika email ditemukan
        if ($result) {
            $user_id = $result['user_id'];
            $token = bin2hex(random_bytes(50)); // Buat token pemulihan
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?');
            $stmt->execute([$token, $user_id]);

            // Kirim email pemulihan menggunakan PHPMailer dengan debug mode
            $mail = new PHPMailer(true);

            try {
                // Konfigurasi server email
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Ganti dengan SMTP server yang benar
                $mail->SMTPAuth = true;
                $mail->Username = 'anandanailafadlula28@gmail.com'; // Ganti dengan email Anda
                $mail->Password = 'Bismillahduniadanakhiratsukses@@'; // Ganti dengan password email Anda
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Aktifkan debug mode untuk mendapatkan informasi lebih lanjut
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktifkan debug mode

                // Penerima dan pengirim
                $mail->setFrom('anandanailafadlula28@gmail.com', 'No Reply'); // Sesuaikan pengirim
                $mail->addAddress($email);

                // Konten email
                $mail->isHTML(true);
                $mail->Subject = "Password Reset Request";
                $mail->Body    = "Klik link berikut untuk mereset password Anda: <a href='https://example.com/reset_password.php?token=" . $token . "'>Reset Password</a>";

                if (!$mail->send()) {
                    throw new Exception("Email could not be sent. Mailer Error: " . $mail->ErrorInfo);
                }
                $_SESSION['message'] = "Email pemulihan telah dikirim!";
            } catch (Exception $e) {
                // Debugging error PHPMailer
                error_log("PHPMailer Error: " . $mail->ErrorInfo);
                $_SESSION['error'] = "Gagal mengirim email pemulihan: " . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = "Email tidak terdaftar.";
        }
    } else {
        $_SESSION['error'] = "Email tidak boleh kosong.";
    }
} else {
    $_SESSION['error'] = "Metode pengiriman tidak valid.";
}

header('Location: lupa_password.php');
?>
