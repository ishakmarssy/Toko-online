<?php
include("../assets/kon.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Anda harus login."]);
    exit();
}

$id_keranjang = $_POST['id_keranjang'];
$action = $_POST['action'];

$query = "SELECT jumlah, produk_id FROM keranjang WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_keranjang);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["status" => "error", "message" => "Produk tidak ditemukan."]);
    exit();
}

$jumlah = $row['jumlah'];
if ($action === "tambah") {
    $jumlah++;
} elseif ($action === "kurang" && $jumlah > 1) {
    $jumlah--;
}

$update = "UPDATE keranjang SET jumlah = ? WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ii", $jumlah, $id_keranjang);
$stmt->execute();

$total_query = "SELECT SUM(p.harga * k.jumlah) AS total_harga FROM keranjang k JOIN produk p ON k.produk_id = p.id WHERE k.user_id = ?";
$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_harga = $total_row['total_harga'];

echo json_encode(["status" => "success", "jumlah" => $jumlah, "total_harga" => $total_harga]);
?>
