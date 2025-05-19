<?php
session_start();
include("../assets/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Ambil ID admin dari sesi (bukan dari form)
$admin_id = $_SESSION["admin_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $harga_lama = $_POST['harga_lama'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];

    // Proses upload gambar dengan validasi
    $target_dir = "../uploads/";
    $gambar = basename($_FILES["gambar"]["name"]);
    $target_file = $target_dir . $gambar;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file adalah gambar
    $check = getimagesize($_FILES["gambar"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File bukan gambar!'); window.history.back();</script>";
        exit();
    }

    // Validasi format gambar
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        echo "<script>alert('Format gambar harus JPG, JPEG, PNG, atau GIF!'); window.history.back();</script>";
        exit();
    }

    // Batasi ukuran gambar (contoh: max 2MB)
    if ($_FILES["gambar"]["size"] > 2097152) {
        echo "<script>alert('Ukuran gambar terlalu besar! (Max: 2MB)'); window.history.back();</script>";
        exit();
    }

    // Pindahkan file yang diunggah
    if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        echo "<script>alert('Gagal mengupload gambar!'); window.history.back();</script>";
        exit();
    }

    // Simpan ke database
    $sql = "INSERT INTO produk (nama, kategori, harga, harga_lama, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiss", $nama, $kategori, $harga, $harga_lama, $stok, $deskripsi, $gambar);

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='index.php#produk';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
