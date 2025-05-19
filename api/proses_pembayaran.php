<?php
//session_start();
include("../assets/kon.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../assets/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$alamat = $_POST["alamat"];
$pengiriman_id = $_POST["pengiriman_id"];
$hp_id = $_POST["hp_id"];
$metode_pembayaran_id = $_POST["metode_pembayaran_id"];
$total_harga = $_POST["total"];

// Ambil biaya pengiriman
$query_pengiriman = "SELECT biaya FROM pengiriman WHERE id = ?";
$stmt_pengiriman = $conn->prepare($query_pengiriman);
$stmt_pengiriman->bind_param("i", $pengiriman_id);
$stmt_pengiriman->execute();
$result_pengiriman = $stmt_pengiriman->get_result();
$biaya_pengiriman = $result_pengiriman->fetch_assoc()["biaya"];
$stmt_pengiriman->close();

// Hitung total akhir
$total_harga_final = $total_harga + $biaya_pengiriman;

// Ambil nomor rekening dari metode pembayaran
$query_rekening = "SELECT nomor_rekening FROM metode_pembayaran WHERE id = ?";
$stmt_rekening = $conn->prepare($query_rekening);
$stmt_rekening->bind_param("i", $metode_pembayaran_id);
$stmt_rekening->execute();
$result_rekening = $stmt_rekening->get_result();
$rekening = $result_rekening->fetch_assoc()["nomor_rekening"];
$stmt_rekening->close();

// Ambil produk dari keranjang dan cek stok
$query_keranjang = "SELECT k.produk_id, k.jumlah, p.harga, p.stok, p.nama
                    FROM keranjang k
                    JOIN produk p ON k.produk_id = p.id
                    WHERE k.user_id = ?";
$stmt_keranjang = $conn->prepare($query_keranjang);
$stmt_keranjang->bind_param("i", $user_id);
$stmt_keranjang->execute();
$result_keranjang = $stmt_keranjang->get_result();

$produk_keranjang = [];
while ($row = $result_keranjang->fetch_assoc()) {
    if ($row["jumlah"] > $row["stok"]) {
        echo "<script>alert('Maaf produk {$row['nama']} telah habis terjual, Stok: {$row['stok']}'); window.location.href='checkout_keranjang.php';</script>";
        exit();
    }
    $produk_keranjang[] = $row;
}
$stmt_keranjang->close();

// Simpan pesanan ke tabel `pesanan`
$query_pesanan = "INSERT INTO pesanan (user_id, alamat, pengiriman_id, metode_pembayaran_id, nomor_rekening, biaya_pengiriman, total_harga, hp_id, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
$stmt_pesanan = $conn->prepare($query_pesanan);
$stmt_pesanan->bind_param("isiisdds", $user_id, $alamat, $pengiriman_id, $metode_pembayaran_id, $rekening, $biaya_pengiriman, $total_harga, $hp_id);

if ($stmt_pesanan->execute()) {
    $pesanan_id = $stmt_pesanan->insert_id;

    foreach ($produk_keranjang as $produk) {
        $produk_id = $produk["produk_id"];
        $jumlah = $produk["jumlah"];
        $harga = $produk["harga"];
        $subtotal = $jumlah * $harga;

        // Simpan detail pesanan
        $query_detail = "INSERT INTO pesanan_detail (pesanan_id, produk_id, jumlah, subtotal) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($query_detail);
        $stmt_detail->bind_param("iiid", $pesanan_id, $produk_id, $jumlah, $subtotal);
        $stmt_detail->execute();
        $stmt_detail->close();

        // Kurangi stok produk
        $query_update_stok = "UPDATE produk SET stok = stok - ? WHERE id = ?";
        $stmt_update_stok = $conn->prepare($query_update_stok);
        $stmt_update_stok->bind_param("ii", $jumlah, $produk_id);
        $stmt_update_stok->execute();
        $stmt_update_stok->close();
    }

    // Hapus produk dari keranjang setelah pesanan dibuat
    $sql_delete = "DELETE FROM keranjang WHERE user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    echo "<script>alert('Pesanan berhasil dibuat! Total yang harus dibayar: Rp" . number_format($total_harga, 0, ',', '.') . "'); window.location='status_pesanan.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan, silakan coba lagi.'); window.location='checkout_keranjang.php';</script>";
}

$stmt_pesanan->close();
$conn->close();
?>

