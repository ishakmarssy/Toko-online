<?php
include("kon.php");

session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Anda harus login terlebih dahulu."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$produk_id = $_POST["produk_id"];
$jumlah = $_POST["jumlah"];
$total_harga = $_POST["total"];

// Ambil saldo user
$query_saldo = "SELECT saldo FROM users WHERE id = '$user_id'";
$result_saldo = $conn->query($query_saldo);
$row_saldo = $result_saldo->fetch_assoc();
$saldo_user = $row_saldo['saldo'];

if ($saldo_user < $total_harga) {
    echo json_encode(["status" => "error", "message" => "Saldo tidak mencukupi."]);
    exit();
}

// Kurangi saldo user
$new_saldo = $saldo_user - $total_harga;
$query_update_saldo = "UPDATE users SET saldo = '$new_saldo' WHERE id = '$user_id'";
$conn->query($query_update_saldo);

// Simpan transaksi
$query_transaksi = "INSERT INTO transaksi (user_id, produk_id, jumlah, total_harga, metode_pembayaran, status, created_at) 
                    VALUES ('$user_id', '$produk_id', '$jumlah', '$total_harga', 'Saldo', 'Selesai', NOW())";
$conn->query($query_transaksi);

// Hapus item dari sesi beli sekarang
unset($_SESSION["beli_sekarang"]);

echo "<script>alert('Pembayaran berhasil! Pesanan Anda telah diproses.'); window.location='../index.php';</script>";
?>
