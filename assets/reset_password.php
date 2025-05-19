<?php
include("kon.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan token tersedia di URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Token tidak valid!");
}

$token = $_GET['token'];

// Validasi token dengan memeriksa tabel password_resets
$sql_token = "SELECT user_id, created_at FROM password_resets WHERE token = ? LIMIT 1";
$stmt_token = $conn->prepare($sql_token);
$stmt_token->bind_param("s", $token);
$stmt_token->execute();
$result_token = $stmt_token->get_result();

if ($result_token->num_rows == 0) {
    die("Token tidak valid atau sudah kadaluarsa!");
}

$row_token = $result_token->fetch_assoc();
$user_id = $row_token['user_id'];
// TODO: Anda bisa menambahkan pengecekan kadaluarsa token (misalnya, jika created_at lebih dari 1 jam yang lalu)
$stmt_token->close();

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($new_password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (empty($new_password)) {
        $error = "Password tidak boleh kosong!";
    } else {
        // Hash password baru menggunakan MD5
        $hashed_password = md5($new_password);
        
        $sql_update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hashed_password, $user_id);
        if ($stmt_update->execute()) {
            $success = "Password berhasil diubah. Silakan <a href='login.php'>login</a> dengan password baru.";
            
            // Hapus token reset agar tidak dapat digunakan kembali
            $sql_delete = "DELETE FROM password_resets WHERE token = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $token);
            $stmt_delete->execute();
            $stmt_delete->close();
        } else {
            $error = "Gagal mengubah password!";
        }
        $stmt_update->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
   <title>Reset Password - Toko Online</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
   <style>
        /* Background full-screen dengan gradient gelap ala TikTok */
        body {
            background: linear-gradient(135deg, #000000, #1A1A1A);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
        }
        /* Container form reset password */
        .reset-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }
        .reset-container h3 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        /* Styling input */
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }
        .form-control:focus {
            box-shadow: none;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: #fff;
        }
        .input-group-text {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.2rem;
        }
        /* Tombol gradient ala TikTok */
        .btn-tiktok {
            background: linear-gradient(45deg, #FE2C55, #F5317F);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            transition: background 0.3s ease;
            width: 100%;
        }
        .btn-tiktok:hover {
            background: linear-gradient(45deg, #F5317F, #FE2C55);
        }
        a {
            color: #F5317F;
        }
        a:hover {
            color: #FE2C55;
        }
   </style>
</head>
<body>
    <div class="reset-container">
        <h3 class="text-center">Reset Password</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password" required>
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-tiktok">Ubah Password</button>
        </form>
    </div>
</body>
</html>
