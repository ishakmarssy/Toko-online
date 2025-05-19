<?php
include("../assets/kon.php");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi gagal!"]));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die(json_encode(["success" => false, "message" => "ID tidak valid!"]));
}

$topup_id = intval($_GET['id']);
$sql = "SELECT status FROM topup WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topup_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die(json_encode(["success" => false, "message" => "Transaksi tidak ditemukan!"]));
}

echo json_encode(["success" => true, "status" => $data['status']]);
