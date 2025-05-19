<?php
session_start();
include("../assets/kon.php"); // Pastikan file koneksi database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = md5(trim($_POST["password"])); // Enkripsi dengan MD5

    // Query untuk mencari admin berdasarkan username dan password
    $query = "SELECT id, username FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        // Redirect ke dashboard admin
        header("Location: index.php");
        exit();
    } else {
        // Jika login gagal, kembali ke halaman login dengan pesan error
        echo "<script>alert('Username atau password salah!'); window.location='login.php';</script>";
        exit();
    }
} else {
    // Jika bukan metode POST, redirect ke halaman login
    header("Location: login.php");
    exit();
}
?>
