<?php
session_start();
include("../db/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "toko_online");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal!"]);
    exit;
}

if (!isset($_POST['id'], $_POST['status'])) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap!"]);
    exit;
}

$id = intval($_POST['id']);
$status = $_POST['status'];

if (!in_array($status, ['success', 'failed'])) {
    echo json_encode(["success" => false, "message" => "Status tidak valid!"]);
    exit;
}

// Ambil data top-up
$sql = "SELECT * FROM topup WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$topup = $result->fetch_assoc();

if (!$topup) {
    echo json_encode(["success" => false, "message" => "Top-Up tidak ditemukan!"]);
    exit;
}

if ($topup['status'] != 'pending') {
    echo json_encode(["success" => false, "message" => "Top-Up sudah diproses sebelumnya!"]);
    exit;
}

// Update status top-up
$sql = "UPDATE topup SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);
if ($stmt->execute()) {
    // Jika sukses, tambahkan saldo user jika status sukses
    if ($status == 'success') {
        $user_id = $topup['user_id'];
        $jumlah = $topup['jumlah'];
        $conn->query("UPDATE users SET saldo = saldo + $jumlah WHERE id = $user_id");
    }
    echo json_encode(["success" => true, "message" => "Top-Up berhasil diperbarui!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui top-up!"]);
}

$stmt->close();
$conn->close();
?>
