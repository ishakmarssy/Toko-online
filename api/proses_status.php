<?php
include("../assets/kon.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pastikan id dan status tersedia
    if (!isset($_POST["id"]) || !isset($_POST["status"])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap!"]);
        exit();
    }

    $pesanan_id = intval($_POST["id"]); // Pastikan id adalah angka
    $status_baru = $_POST["status"];

    // Status yang diperbolehkan
    $status_valid = ["Pending", "Diproses", "Dikirim", "Selesai", "Gagal", "Dibatalkan"];
    if (!in_array($status_baru, $status_valid)) {
        http_response_code(400);
        echo json_encode(["error" => "Status tidak valid!"]);
        exit();
    }

    // Update status pesanan menggunakan prepared statement
    $query = "UPDATE pesanan SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Gagal menyiapkan query!"]);
        exit();
    }

    $stmt->bind_param("si", $status_baru, $pesanan_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Status berhasil diperbarui!"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Gagal memperbarui status."]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Akses tidak diizinkan!"]);
}
?>
