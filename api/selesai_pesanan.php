<?php
require '../assets/kon.php'; // Pastikan ada koneksi ke database
session_start(); // Mulai sesi untuk menyimpan alert

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_pesanan"])) {
    $id_pesanan = intval($_POST["id_pesanan"]);

    // Ambil status pesanan terlebih dahulu
    $query = $conn->prepare("SELECT status FROM pesanan WHERE id = ?");
    $query->bind_param("i", $id_pesanan);
    $query->execute();
    $result = $query->get_result();
    $pesanan = $result->fetch_assoc();

    if ($pesanan && $pesanan["status"] == "Dikirim") {
        // Update status pesanan menjadi "Selesai"
        $update = $conn->prepare("UPDATE pesanan SET status = 'Selesai' WHERE id = ?");
        $update->bind_param("i", $id_pesanan);

        if ($update->execute()) {
            $_SESSION['alert'] = [
                "type" => "success",
                "message" => "Pesanan berhasil diselesaikan!"
            ];
        } else {
            $_SESSION['alert'] = [
                "type" => "error",
                "message" => "Gagal menyelesaikan pesanan!"
            ];
        }
    } else {
        $_SESSION['alert'] = [
            "type" => "warning",
            "message" => "Pesanan tidak dapat diselesaikan!"
        ];
    }
}

// Redirect ke status_pesanan.php untuk menampilkan SweetAlert
header("Location: status_pesanan.php");
exit();
?>
