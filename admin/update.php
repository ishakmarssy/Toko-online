<?php
session_start();
include("../assets/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga_lama = $_POST['harga_lama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $kategori  = $_POST['kategori'];

    // Cek apakah ada file gambar yang diupload
    if (!empty($_FILES["gambar"]["name"])) {
        $target_dir = "../uploads/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

        // Update dengan gambar baru
        $sql = "UPDATE produk SET nama=?, harga_lama=?, harga=?, deskripsi=?, gambar=?, stok=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssii", $nama, $harga_lama, $harga, $deskripsi, $gambar, $stok, $id);
    } else {
        // Update tanpa mengubah gambar
        $sql = "UPDATE produk SET nama=?, kategori=?, harga_lama=?, harga=?, deskripsi=?, stok=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiisii", $nama, $kategori, $harga_lama, $harga, $deskripsi, $stok, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='index.php#produk';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui produk!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
