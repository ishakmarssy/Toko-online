<?php
include("kon.php");

if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu!";
    exit();
}

$user_id = $_SESSION['user_id'];
$old_password = md5($_POST['old_password']); // Enkripsi password lama
$new_password = md5($_POST['new_password']); // Enkripsi password baru
$confirm_password = md5($_POST['confirm_password']); // Enkripsi konfirmasi password

$conn = new mysqli("localhost", "root", "", "toko_online");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil password lama dari database
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($password_hash);
$stmt->fetch();
$stmt->close();

if ($old_password !== $password_hash) { // Bandingkan password lama
    echo "Password lama salah!";
    exit();
}

if ($new_password !== $confirm_password) { // Cek konfirmasi password
    echo "Konfirmasi password tidak cocok!";
    exit();
}

// Update password baru
$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_password, $user_id);

if ($stmt->execute()) {
    echo "Password berhasil diubah!";
} else {
    echo "Gagal mengubah password!";
}

$stmt->close();
$conn->close();
?>
