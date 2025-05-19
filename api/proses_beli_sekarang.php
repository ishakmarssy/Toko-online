<?php
session_start(); // Tambahkan session_start()
include("../assets/kon.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../assets/login.php");
    exit();
}

if (!isset($_SESSION["beli_sekarang"])) {
    $_SESSION["pesan_error"] = "Tidak ada produk untuk dibeli!";
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$produk = $_SESSION["beli_sekarang"];
$produk_id = $produk["id"];
$jumlah = $produk["jumlah"];
$harga_produk = $produk["harga"];
$subtotal = $harga_produk * $jumlah;
$alamat = $_POST["alamat"];
$hp_id = $_POST["hp_id"];
$pengiriman_id = $_POST["pengiriman_id"];
$metode_pembayaran_id = $_POST["metode_pembayaran_id"];

// Cek stok produk sebelum memproses pesanan
$query_stok = "SELECT stok, nama FROM produk WHERE id = ?";
$stmt_stok = $conn->prepare($query_stok);
$stmt_stok->bind_param("i", $produk_id);
$stmt_stok->execute();
$result_stok = $stmt_stok->get_result();
$produk_data = $result_stok->fetch_assoc();
$stmt_stok->close();

if ($produk_data["stok"] < $jumlah) {
    $_SESSION["pesan_error"] = "Stok produk {$produk_data['nama']} tidak mencukupi! Stok tersedia: {$produk_data['stok']}";
    header("Location: ../index.php");
    exit();
}

// Ambil biaya pengiriman dari database
$query_pengiriman = "SELECT biaya FROM pengiriman WHERE id = ?";
$stmt_pengiriman = $conn->prepare($query_pengiriman);
$stmt_pengiriman->bind_param("i", $pengiriman_id);
$stmt_pengiriman->execute();
$result_pengiriman = $stmt_pengiriman->get_result();
$biaya_pengiriman = $result_pengiriman->fetch_assoc()["biaya"];
$stmt_pengiriman->close();

// Hitung total harga (termasuk biaya pengiriman)
$total_harga = $subtotal + $biaya_pengiriman;

// Ambil nomor rekening dari database
$query_rekening = "SELECT nomor_rekening FROM metode_pembayaran WHERE id = ?";
$stmt_rekening = $conn->prepare($query_rekening);
$stmt_rekening->bind_param("i", $metode_pembayaran_id);
$stmt_rekening->execute();
$result_rekening = $stmt_rekening->get_result();
$rekening = $result_rekening->fetch_assoc()["nomor_rekening"];
$stmt_rekening->close();

// Simpan pesanan ke tabel `pesanan`
$query_pesanan = "INSERT INTO pesanan (user_id, alamat, pengiriman_id, metode_pembayaran_id, nomor_rekening, biaya_pengiriman, total_harga, hp_id, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
$stmt_pesanan = $conn->prepare($query_pesanan);
$stmt_pesanan->bind_param("isiisdds", $user_id, $alamat, $pengiriman_id, $metode_pembayaran_id, $rekening, $biaya_pengiriman, $total_harga, $hp_id);

if ($stmt_pesanan->execute()) {
    $pesanan_id = $stmt_pesanan->insert_id;

    // Simpan detail pesanan
    $query_detail = "INSERT INTO pesanan_detail (pesanan_id, produk_id, jumlah, subtotal) VALUES (?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($query_detail);
    $stmt_detail->bind_param("iiid", $pesanan_id, $produk_id, $jumlah, $subtotal);
    $stmt_detail->execute();
    $stmt_detail->close();

    // Kurangi stok produk setelah pesanan dibuat
    $query_update_stok = "UPDATE produk SET stok = stok - ? WHERE id = ?";
    $stmt_update_stok = $conn->prepare($query_update_stok);
    $stmt_update_stok->bind_param("ii", $jumlah, $produk_id);
    $stmt_update_stok->execute();
    $stmt_update_stok->close();

    // Hapus sesi beli_sekarang setelah pesanan dibuat
    unset($_SESSION["beli_sekarang"]);

    $_SESSION["pesan_sukses"] = "Pesanan berhasil dibuat! Silakan lakukan pembayaran.";
    header("Location: ../api/status_pesanan.php");
    exit();
} else {
    $_SESSION["pesan_error"] = "Terjadi kesalahan, silakan coba lagi.";
    header("Location: ../index.php");
    exit();
}

$stmt_pesanan->close();
$conn->close();
?>
