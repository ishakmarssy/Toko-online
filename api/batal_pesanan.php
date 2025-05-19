<?php
require '../assets/kon.php';
session_start(); // Pastikan sesi dimulai

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_pesanan"])) {
    $id_pesanan = intval($_POST["id_pesanan"]);

    // Ambil status pesanan terlebih dahulu
    $query = $conn->prepare("SELECT status FROM pesanan WHERE id = ?");
    $query->bind_param("i", $id_pesanan);
    $query->execute();
    $result = $query->get_result();
    $pesanan = $result->fetch_assoc();

    if ($pesanan && $pesanan["status"] == "Pending") {
        // Ambil daftar produk yang dipesan
        $query_produk = $conn->prepare("SELECT produk_id, jumlah FROM pesanan_detail WHERE pesanan_id = ?");
        $query_produk->bind_param("i", $id_pesanan);
        $query_produk->execute();
        $result_produk = $query_produk->get_result();

        while ($produk = $result_produk->fetch_assoc()) {
            $produk_id = $produk["produk_id"];
            $jumlah = $produk["jumlah"];

            // Kembalikan stok produk
            $update_stok = $conn->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?");
            $update_stok->bind_param("ii", $jumlah, $produk_id);
            $update_stok->execute();
        }

        // Update status pesanan menjadi "Dibatalkan"
        $update = $conn->prepare("UPDATE pesanan SET status = 'Dibatalkan' WHERE id = ?");
        $update->bind_param("i", $id_pesanan);
        
        if ($update->execute()) {
            // Tampilkan SweetAlert di status_pesanan.php
            $_SESSION['alert'] = [
                "type" => "success",
                "message" => "Pesanan berhasil dibatalkan!"
            ];
        } else {
            $_SESSION['alert'] = [
                "type" => "error",
                "message" => "Gagal membatalkan pesanan!"
            ];
        }
    } else {
        $_SESSION['alert'] = [
            "type" => "warning",
            "message" => "Pesanan tidak dapat dibatalkan!"
        ];
    }
}

// Redirect kembali ke status_pesanan.php
header("Location: status_pesanan.php");
exit();
?>
