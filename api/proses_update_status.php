<?php
session_start();
include("../assets/kon.php");



// Periksa apakah ada data yang dikirim
if (isset($_POST["id_pesanan"]) && isset($_POST["status"])) {
    $id_pesanan = $_POST["id_pesanan"];
    $status = $_POST["status"];

    // Koneksi ke database
    //$conn = new mysqli("localhost", "root", "", "toko_online");
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Update status pesanan
    $sql = "UPDATE pembayaran SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id_pesanan);
    
    if ($stmt->execute()) {
        echo "<script>alert('Status pesanan diperbarui!'); window.location='../api/detail_pesanan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status!'); window.location='../admin/admin.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../admin/admin.php");
    exit();
}
?>
