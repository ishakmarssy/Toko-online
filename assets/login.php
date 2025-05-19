<?php
include("kon.php");

// Inisialisasi nilai default
$store_name = "Nama Toko Default";

// Query untuk mengambil nama toko
$sql = "SELECT store_name FROM store_settings WHERE id = 1";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_name = $row["store_name"] ?? $store_name;
    }
    $result->free();
}

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); // Hash password dengan MD5
    $role = $_POST['role']; // Nilai role diambil dari input tersembunyi

    

    // Gunakan prepared statement
    $sql = "SELECT id, nama, password, role FROM users WHERE username = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Periksa apakah password cocok dengan hash di database (gunakan md5)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            if ($role == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username atau peran tidak ditemukan!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Toko Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
/* Global */
body {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb); /* Gradien lembut */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: #333;
    font-family: 'Poppins', sans-serif; /* Font modern */
    margin: 0;
}

/* Container Login */
.login-container {
    width: 100%;
    max-width: 400px;
    padding: 30px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
    text-align: center;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-container:hover {
    transform: translateY(-5px);
    box-shadow: 0px 6px 25px rgba(0, 0, 0, 0.15);
}

/* Judul Form */
.login-container h3 {
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

/* Tombol Login */
.btn-bukalapak {
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

.btn-bukalapak:hover {
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
}

/* Input Group */
.input-group-text {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-right: none;
    color: #333;
    font-size: 16px;
    border-radius: 8px 0 0 8px;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
}

/* Responsivitas */
@media (max-width: 576px) {
    .login-container {
        padding: 20px;
    }

    .login-container h3 {
        font-size: 20px;
    }

    .btn-bukalapak {
        font-size: 14px;
        padding: 10px;
    }
}
    </style>
</head>

<body>
    <div class="login-container">
        <h3><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-cart-plus-fill"></i> Shop</h3>
        <?php if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        } ?>
        <hr>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Sembunyikan "Login sebagai" dengan input tersembunyi -->
            <input type="hidden" name="role" value="user">
            <!-- Login Button -->
            <button type="submit" name="submit" class="btn btn-bukalapak">Masuk</button>
        </form>
        
        <p class="mt-3 text-center">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
        <p class="mt-2 text-center">
            <a href="forgot_password.php">Lupa Password?</a>
        </p>
    </div>
</body>

</html>