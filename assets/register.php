<?php
//session_start();
include("kon.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $hp = trim($_POST['hp']);
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); // Hash password dengan MD5
    $role = "user"; // Default user



    // Cek apakah username sudah digunakan
    $cek_username = $conn->prepare("SELECT id FROM users WHERE username=?");
    $cek_username->bind_param("s", $username);
    $cek_username->execute();
    $cek_username->store_result();

    if ($cek_username->num_rows > 0) {
        $pesan = "Username sudah terdaftar, gunakan yang lain!";
    } else {
        // Insert ke database dengan prepared statement
        $stmt = $conn->prepare("INSERT INTO users (nama, email, hp, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama, $email, $hp, $username, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_nama'] = $nama;
            $_SESSION['role'] = $role;
            header("Location: index.php");
            exit();
        } else {
            $pesan = "Gagal mendaftar, coba lagi.";
        }
        $stmt->close();
    }

    $cek_username->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register - Toko Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global */
body {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb); /* Gradien lembut */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: 'Poppins', sans-serif; /* Font modern */
    margin: 0;
    color: #333;
}

/* Container Register */
.register-container {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
    max-width: 400px;
    width: 100%;
    text-align: center;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.register-container:hover {
    transform: translateY(-5px);
    box-shadow: 0px 6px 25px rgba(0, 0, 0, 0.15);
}

/* Judul Form */
.register-container h3 {
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Input Form */
.form-control {
    background: #f9f9f9;
    border: 1px solid #ddd;
    color: #333;
    padding: 12px 15px;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.05);
}

.form-control:focus {
    box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.3);
    border-color: #007bff;
}

/* Tombol Register */
.btn-register {
    background: #007bff;
    border: none;
    padding: 12px;
    border-radius: 50px;
    font-weight: bold;
    transition: all 0.3s ease;
    color: #fff;
    width: 100%;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0px 4px 10px rgba(0, 123, 255, 0.3);
}

.btn-register:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0px 6px 15px rgba(0, 123, 255, 0.4);
}

/* Link */
a {
    color: #007bff;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* Alert */
.alert {
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 15px;
    background: #ffefc1;
    border: 2px solid #ffcc00;
    color: #333;
}

/* Responsivitas */
@media (max-width: 576px) {
    .register-container {
        padding: 20px;
    }

    .register-container h3 {
        font-size: 20px;
    }

    .btn-register {
        font-size: 14px;
        padding: 10px;
    }
}
    </style>
</head>
<body>
    <div class="register-container">
        <h3>Daftar Akun</h3>
        <?php if (isset($pesan)) { echo "<div class='alert alert-warning'>$pesan</div>"; } ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="hp" class="form-control" placeholder="Masukkan nomor HP" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <hr>
            <button type="submit" class="btn btn-register w-100">Daftar</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>