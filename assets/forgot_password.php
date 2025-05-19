<?php
// forgot_password.php

include("kon.php");

// Ambil pengaturan email dari database
$sql = "SELECT * FROM email_settings WHERE id = 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $emailSettings = $result->fetch_assoc();
} else {
    // Nilai default jika tidak ditemukan
    $emailSettings = [
        'username'   => 'abulekegroup@gmail.com',
        'password'   => 'tnbb gfoo xbkq cjre',
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'encryption' => 'tls'
    ];
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Pastikan path-nya sesuai dengan struktur proyek

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    
    if (!empty($username)) {
        // Cek apakah username atau email terdaftar
        $sql = "SELECT id, email FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User ditemukan
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $email = $user['email'];
            
            // Generate token reset password
            $token = bin2hex(random_bytes(16));
            
            // Simpan token ke dalam tabel password_resets
            $insert_sql = "INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $user_id, $token);
            if ($insert_stmt->execute()) {
                // Buat link reset password (ubah yourdomain.com dengan domain Anda)
                $reset_link = "https://yourdomain.com/assets/reset_password.php?token=" . $token;
                $mail = new PHPMailer(true);
                try {
                    // Konfigurasi SMTP menggunakan pengaturan dari database
                    $mail->isSMTP();
                    $mail->Host       = $emailSettings['host'];
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $emailSettings['username'];
                    $mail->Password   = $emailSettings['password'];
                    if ($emailSettings['encryption'] == 'tls') {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    } elseif ($emailSettings['encryption'] == 'ssl') {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    }
                    $mail->Port       = $emailSettings['port'];
                    $mail->setFrom($emailSettings['username'], 'Toko Online');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password - Toko Online';
                    $mail->Body    = "Halo,<br><br>Kami menerima permintaan untuk mereset password Anda.<br>
                                      Silakan klik link berikut untuk mereset password:<br><br>
                                      <a href='" . $reset_link . "'>" . $reset_link . "</a><br><br>
                                      Jika Anda tidak meminta reset password, abaikan email ini.";
                    $mail->AltBody = "Halo,\n\nKami menerima permintaan untuk mereset password Anda.\n" .
                                     "Silakan klik link berikut untuk mereset password:\n\n" .
                                     $reset_link . "\n\n" .
                                     "Jika Anda tidak meminta reset password, abaikan email ini.";
                    $mail->send();
                    $success = "Link reset password telah dikirim ke email Anda.";
                } catch (Exception $e) {
                    $error = "Gagal mengirim email reset password. Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                $error = "Gagal menyimpan token reset password!";
            }
            $insert_stmt->close();
        } else {
            $error = "Akun tidak ditemukan!";
        }
        $stmt->close();
    } else {
        $error = "Masukkan username atau email!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Lupa Password - Toko Online</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
   <style>
        /* Background full-screen dengan gradient modern */
        body {
            background: linear-gradient(135deg, #1A1A1A, #000000);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
        }
        /* Card container */
        .forgot-container {
            width: 100%;
            max-width: 380px;
            background: #222;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            color: #fff;
            box-shadow: 0px 10px 20px rgba(255, 0, 150, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .forgot-container:hover {
            transform: translateY(-5px);
            box-shadow: 0px 12px 25px rgba(255, 0, 150, 0.4);
        }

        .forgot-container h3 {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 22px;
        }

        /* Styling input */
        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            padding: 12px 15px;
            border-radius: 8px;
        }

        .form-control:focus {
            box-shadow: none;
            background: rgba(255, 255, 255, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.3rem;
        }

        /* Tombol gradient ala TikTok */
        .btn-tiktok {
            background: linear-gradient(45deg, #FE2C55, #F5317F);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            width: 100%;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-tiktok:hover {
            background: linear-gradient(45deg, #F5317F, #FE2C55);
            transform: scale(1.02);
        }

        /* Link */
        a {
            color: #F5317F;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #FE2C55;
        }
   </style>
</head>
<body>
    <div class="forgot-container">
        <h3><i class="bi bi-shield-lock"></i> Lupa Password</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-danger p-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success p-2"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="forgot_password.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username atau email" required>
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-tiktok">🔐 Reset Password</button>
        </form>
        
        <p class="mt-3">
            Ingat password? <a href="login.php">Kembali ke Login</a>
        </p>
    </div>
</body>
</html>