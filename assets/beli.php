<?php
session_start();
if (!isset($_POST["produk_id"]) || !isset($_POST["jumlah"])) {
    header("Location: ../index.php");
    exit();
}

$produk_id = $_POST["produk_id"];
$jumlah = (int) $_POST["jumlah"]; // Pastikan jumlah adalah angka

include("kon.php");

// Ambil data stok dari database
$query = $conn->prepare("SELECT stok FROM produk WHERE id = ?");
$query->bind_param("i", $produk_id);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    echo "Produk tidak ditemukan!";
    exit();
}

$stok_tersedia = $produk['stok'];

if ($jumlah > $stok_tersedia) {
    echo "<script>alert('Maaf, stok tidak mencukupi! Maksimum pembelian adalah $stok_tersedia'); window.history.back();</script>";
    exit();
}


// Ambil data produk berdasarkan ID
$sql = "SELECT * FROM produk WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $produk_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $produk = $result->fetch_assoc();

    // Simpan data ke sesi (session) untuk checkout
    $_SESSION["beli_sekarang"] = [
        "id" => $produk["id"],
        "nama" => $produk["nama"],
        "harga" => $produk["harga"],
        "jumlah" => $jumlah,
        "gambar" => $produk["gambar"]
    ];

    // Redirect ke halaman checkout
    header("Location: checkout.php");
} else {
    echo "Produk tidak ditemukan.";
}

$conn->close();
?>
