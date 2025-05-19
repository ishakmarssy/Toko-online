<?php
//session_start();
header('Content-Type: application/json');
include("../assets/kon.php");

// Pastikan pengguna sudah login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($user_id == 0) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Perbaikan query untuk mencegah kesalahan saat mengambil jumlah item dalam keranjang
$query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM keranjang WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'count' => 0, 'message' => 'Failed to prepare statement']);
    exit();
}

$sql = "SELECT SUM(jumlah) AS total FROM keranjang WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total = $row['total'] ?? 0;

echo json_encode(['success' => true, 'count' => (int)$total]);
$stmt->close();
$conn->close();
?>