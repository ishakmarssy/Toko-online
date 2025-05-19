<?php
session_start();
include("../assets/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus data terkait di tabel yang memiliki foreign key produk_id
    $tables = ['keranjang', 'pesanan_detail']; // Sesuaikan dengan tabel terkait
    foreach ($tables as $table) {
        $sql = "DELETE FROM $table WHERE produk_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Ambil nama gambar sebelum menghapus produk
    $sql = "SELECT gambar FROM produk WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produk = $result->fetch_assoc();
    $stmt->close();

    if ($produk) {
        $gambar_path = "../uploads/" . $produk['gambar'];

        // Hapus gambar jika ada
        if (!empty($produk['gambar']) && file_exists($gambar_path)) {
            unlink($gambar_path);
        }

        // Hapus produk dari database
        $sql = "DELETE FROM produk WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Produk berhasil dihapus!'); window.location.href='index.php#produk';</script>";
        } else {
            echo "<script>alert('Gagal menghapus produk!'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('ID produk tidak valid!'); window.history.back();</script>";
}

$conn->close();
?>
