<?php
header('Content-Type: application/json');
session_start();
include 'kon.php';

$response = ["success" => false, "message" => "Terjadi kesalahan."];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Anda harus login terlebih dahulu."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $produk_id = isset($_POST['produk_id']) ? intval($_POST['produk_id']) : 0;
    $jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;

    if ($produk_id <= 0 || $jumlah <= 0) {
        echo json_encode(["success" => false, "message" => "Produk atau jumlah tidak valid."]);
        exit;
    }

    // Cek stok produk
    $stokQuery = $conn->prepare("SELECT stok FROM produk WHERE id = ?");
    $stokQuery->bind_param("i", $produk_id);
    $stokQuery->execute();
    $stokResult = $stokQuery->get_result();

    if ($stokResult->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Produk tidak ditemukan."]);
        exit;
    }

    $row = $stokResult->fetch_assoc();
    $stok_tersedia = (int) $row['stok'];

    // Cek jumlah di keranjang
    $cekQuery = $conn->prepare("SELECT jumlah FROM keranjang WHERE user_id = ? AND produk_id = ?");
    $cekQuery->bind_param("ii", $user_id, $produk_id);
    $cekQuery->execute();
    $result = $cekQuery->get_result();
    $cekQuery->close();

    $total_jumlah = $jumlah;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_jumlah += (int) $row['jumlah'];
    }

    if ($total_jumlah > $stok_tersedia) {
        echo json_encode(["success" => false, "message" => "Stok tidak mencukupi! Stok tersisa: " . $stok_tersedia]);
        exit;
    }

    // Masukkan atau update keranjang
    if ($result->num_rows > 0) {
        $updateQuery = $conn->prepare("UPDATE keranjang SET jumlah = ? WHERE user_id = ? AND produk_id = ?");
        $updateQuery->bind_param("iii", $total_jumlah, $user_id, $produk_id);
        $updateQuery->execute();
        $updateQuery->close();
    } else {
        $insertQuery = $conn->prepare("INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES (?, ?, ?)");
        $insertQuery->bind_param("iii", $user_id, $produk_id, $jumlah);
        $insertQuery->execute();
        $insertQuery->close();
    }

    // Hitung ulang jumlah produk di keranjang untuk badge
    $cartQuery = $conn->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM keranjang WHERE user_id = ?");
    $cartQuery->bind_param("i", $user_id);
    $cartQuery->execute();
    $resultCart = $cartQuery->get_result();
    $cartRow = $resultCart->fetch_assoc();
    $cart_count = (int) $cartRow['total'];

    ob_clean(); // Hapus output sebelumnya
    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil ditambahkan ke keranjang.",
        "count" => $cart_count
    ]);
    exit;
}
?>
