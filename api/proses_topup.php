<?php
session_start();
$conn = new mysqli("localhost", "root", "", "toko_online");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $jumlah = intval($_POST['jumlah']);
    $metode = $_POST['metode'];
    $status = "pending"; // Default status saat pertama kali top-up

    // Simpan data ke database
    $stmt = $conn->prepare("INSERT INTO topup (user_id, jumlah, metode, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $jumlah, $metode, $status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $topup_id = $stmt->insert_id; // Ambil ID terakhir yang dimasukkan
        
        // Debugging: Cek apakah ID berhasil dibuat
        echo "✅ Top-Up berhasil! ID transaksi: $topup_id";

        // Redirect ke halaman menunggu
        header("Location: menunggu_transaksi.php?id=$topup_id");
        exit();
    } else {
        echo "❌ Gagal menyimpan data top-up!";
    }

    $stmt->close();
}

$conn->close();
?>
