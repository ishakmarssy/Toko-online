<?php
include("kon.php");

if (!isset($_POST['id_keranjang'])) {
    echo json_encode(["status" => "error", "message" => "ID tidak valid!"]);
    exit();
}

$id_keranjang = $_POST['id_keranjang'];


if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal!"]);
    exit();
}

$sql = "DELETE FROM keranjang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_keranjang);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Produk berhasil dihapus dari keranjang!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus produk!"]);
}

$stmt->close();
$conn->close();
?>
