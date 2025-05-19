<?php
//session_start();
$conn = new mysqli("localhost", "root", "", "toko_online");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah ID dikirim di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='alert alert-danger text-center'>❌ ID transaksi tidak valid!</div>");
}

$topup_id = intval($_GET['id']);

// Perbaikan Query: Ambil status, jumlah, metode pembayaran, dan nomor rekening
$sql = "SELECT t.status, t.jumlah, m.nomor_rekening, m.nama AS metode
        FROM topup t
        LEFT JOIN metode_pembayaran m ON t.metode = m.nama
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topup_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Jika transaksi tidak ditemukan
if (!$data) {
    die("<div class='alert alert-danger text-center'>❌ Transaksi dengan ID #$topup_id tidak ditemukan!</div>");
}

// Ambil nilai dari hasil query
$status = $data['status'];
$jumlah = number_format($data['jumlah'], 0, ',', '.'); // Format angka Rp
$metode = $data['metode'] ?? 'Tidak diketahui';
$nomor_rekening = $data['nomor_rekening'] ?? '-';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menunggu Konfirmasi Top-Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: rgb(37, 37, 37);
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            background: rgb(255, 255, 255);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            width: 400px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            color: black;
        }
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: #00d9ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            color: white;
        }
        .btn-custom {
            background-color:rgb(109, 188, 202);
            color: #121212;
            border-radius: 25px;
            padding: 10px 20px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #0099cc;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="loader" id="loadingSpinner"></div>
    <h3>Lakukan Pembayaran dan tunggu Konfirmasi</h3>
    <p>Invoice: <strong>#<?= $topup_id ?></strong></p>
    <p>Jumlah: <strong>Rp <?= $jumlah ?></strong></p>
    <p>Via: <strong><?= $metode ?></strong></p>
    <p>Tujuan: <strong><?= $nomor_rekening ?></strong></p>
    <p>Status: <span id="statusBadge" class="status-badge bg-warning text-dark"><?= ucfirst($status) ?></span></p>
    <p id="statusText">Transaksi sedang diproses oleh admin...</p>
    <a href="../index.php" class="btn-custom mt-3">Kembali ke Beranda</a>
</div>

<script>
$(document).ready(function () {
    function cekStatus() {
        $.ajax({
            url: "cek_status_topup.php",
            type: "GET",
            data: { id: <?= $topup_id ?> },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let status = response.status;
                    let badgeClass = "bg-secondary";
                    let statusText = "Transaksi sedang diproses oleh admin..."; // Default

                    if (status === "success") {
                        badgeClass = "bg-success";
                        statusText = "✅ Saldo Telah Masuk!";
                    } else if (status === "failed") {
                        badgeClass = "bg-danger";
                        statusText = "❌ Transaksi Gagal!";
                    } else if (status === "pending") {
                        badgeClass = "bg-warning text-dark";
                    }

                    $("#statusBadge").removeClass().addClass("status-badge " + badgeClass).text(status.charAt(0).toUpperCase() + status.slice(1));
                    $("#statusText").text(statusText);

                    if (status === "success" || status === "failed") {
                        $("#loadingSpinner").hide(); // Hapus animasi loading saat selesai
                        clearInterval(interval);
                    }
                }
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
            }
        });
    }

    let interval = setInterval(cekStatus, 5000); // Cek status setiap 5 detik
});
</script>

</body>
</html>
