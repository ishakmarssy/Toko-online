<?php
session_start();
include("../assets/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];
$conn = new mysqli("localhost", "root", "", "toko_online");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi gagal!"]));
}

if (!isset($_SESSION['admin_id'])) {
    die(json_encode(["success" => false, "message" => "Akses ditolak!"]));
}

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE topup SET status = '$status' WHERE id = '$id'";

if ($conn->query($sql)) {
    // Jika top-up berhasil, tambahkan saldo ke pengguna
    if ($status == "success") {
        $query = "UPDATE users u JOIN topup t ON u.id = t.user_id 
                  SET u.saldo = u.saldo + t.jumlah WHERE t.id = '$id'";
        $conn->query($query);
    }

    die(json_encode(["success" => true, "message" => "Status berhasil diperbarui."]));
} else {
    die(json_encode(["success" => false, "message" => "Terjadi kesalahan!"]));
}
?>
