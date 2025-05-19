<?php
//session_start();
include("../assets/kon.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$alamat = $_POST["alamat"];
$hp_id = $_POST["hp_id"];
$metode_pembayaran_id = $_POST["metode_pembayaran_id"];
$pengiriman_id = $_POST["pengiriman_id"];
$total_harga = (float)$_POST["total_harga"];

// **Simpan data pesanan ke tabel `pesanan`**
$sql_pesanan = "INSERT INTO pesanan (user_id, total_harga, alamat, metode_pembayaran_id, pengiriman_id, hp_id, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
$stmt_pesanan = $conn->prepare($sql_pesanan);
$stmt_pesanan->bind_param("idssss", $user_id, $total_harga, $alamat_pengiriman, $metode_pembayaran, $pengiriman_id, $hp_id);
$stmt_pesanan->execute();
$pesanan_id = $stmt_pesanan->insert_id; // Ambil ID pesanan yang baru saja dibuat
$stmt_pesanan->close();

// **Ambil data produk dari keranjang user**
$sql_keranjang = "SELECT k.produk_id, k.jumlah, p.harga
                  FROM keranjang k
                  JOIN produk p ON k.produk_id = p.id
                  WHERE k.user_id = ?";
$stmt_keranjang = $conn->prepare($sql_keranjang);
$stmt_keranjang->bind_param("i", $user_id);
$stmt_keranjang->execute();
$result_keranjang = $stmt_keranjang->get_result();

// **Simpan produk dari keranjang ke `pesanan_detail`**
while ($row = $result_keranjang->fetch_assoc()) {
    $produk_id = $row["produk_id"];
    $jumlah = $row["jumlah"];
    $subtotal = $jumlah * $row["harga"]; // Hitung subtotal per produk

    $sql_detail = "INSERT INTO pesanan_detail (pesanan_id, produk_id, jumlah, subtotal)
                   VALUES (?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bind_param("iiid", $pesanan_id, $produk_id, $jumlah, $subtotal);
    $stmt_detail->execute();
    $stmt_detail->close();
}

// **Hapus produk dari keranjang setelah checkout sukses**
$sql_hapus_keranjang = "DELETE FROM keranjang WHERE user_id = ?";
$stmt_hapus_keranjang = $conn->prepare($sql_hapus_keranjang);
$stmt_hapus_keranjang->bind_param("i", $user_id);
$stmt_hapus_keranjang->execute();
$stmt_hapus_keranjang->close();

$conn->close();

// **Redirect ke halaman status pesanan**
echo "<script>alert('Checkout berhasil!'); window.location='status_pesanan.php';</script>";
?>
