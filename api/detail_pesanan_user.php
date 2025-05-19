<?php
//session_start();
include("../assets/kon.php");

//if (!isset($_SESSION["admin_id"])) {
//    header("Location: login.php");
//    exit();
//}

if (!isset($_GET["id"])) {
    echo "<script>alert('ID Pesanan tidak ditemukan!'); window.location='admin_pesanan.php';</script>";
    exit();
}

$pesanan_id = $_GET["id"];


// Ambil data pesanan termasuk biaya pengiriman
$query = "SELECT p.id, u.nama AS nama_user, p.alamat, pe.nama AS metode_pengiriman,
                 pe.biaya AS biaya_pengiriman, m.nomor_rekening AS metode_pembayaran,
                 p.total_harga, p.status, p.created_at
          FROM pesanan p
          JOIN users u ON p.user_id = u.id
          JOIN pengiriman pe ON p.pengiriman_id = pe.id
          JOIN metode_pembayaran m ON p.metode_pembayaran_id = m.id
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pesanan_id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();
$stmt->close();

// Ambil detail pesanan
$query_detail = "SELECT pd.produk_id, pr.nama AS nama_produk, pr.harga, pd.jumlah, pd.subtotal
                 FROM pesanan_detail pd
                 JOIN produk pr ON pd.produk_id = pr.id
                 WHERE pd.pesanan_id = ?";
$stmt_detail = $conn->prepare($query_detail);
$stmt_detail->bind_param("i", $pesanan_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$stmt_detail->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Shadows Into Light', cursive; /* Font tulisan tangan */
        }
        .container {
            max-width: 900px;
            font-family: 'Shadows Into Light', cursive; /* Font tulisan tangan */
        }
        /* Kartu dengan Efek Hover */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    background: #ffffff;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Status Badge */
.status-badge {
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

/* Efek Hover untuk Semua Status */
.status-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Status Pending */
.status-pending {
    background-color: #EE5A24;
    color: white;
    animation: pulse 1.5s infinite;
}

/* Status Diproses */
.status-diproses {
    background-color: #17a2b8;
    color: white;
}

/* Status Dikirim */
.status-dikirim {
    background-color: #007bff;
    color: white;
}

/* Status Selesai */
.status-selesai {
    background-color: #28a745;
    color: white;
}

/* Status Gagal */
.status-gagal, .status-dibatalkan {
    background-color: #dc3545;
    color: white;
}

/* Animasi Pulse untuk Status Pending */
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4">
        <h3 class="text-center text-primary mb-4">Invoice <span>#<?= htmlspecialchars($pesanan["id"]) ?></span></h3>

        <table class="table">
            <tr><th>Nama</th><td>: <?= htmlspecialchars($pesanan["nama_user"]) ?></td></tr>
            <tr><th>Alamat</th><td>: <?= htmlspecialchars($pesanan["alamat"]) ?></td></tr>
            <tr><th>Area</th><td>: <?= htmlspecialchars($pesanan["metode_pengiriman"]) ?></td></tr>
            <tr><th>Biaya</th><td>: <strong>Rp.<?= number_format($pesanan["biaya_pengiriman"], 0, ',', '.') ?>.-</strong></td></tr>
            <tr><th  >Pembayaran</th><td>: <?= htmlspecialchars($pesanan["metode_pembayaran"]) ?></td></tr>
            <tr><th>Total</th><td>: <strong>Rp.<?= number_format($pesanan["total_harga"], 0, ',', '.') ?>.-</strong> (Sudah termasuk Ongkir)</td></tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                        $status = strtolower($pesanan["status"]);
                        $statusClass = "status-" . str_replace(" ", "", $status);
                        // Jika status pending, tambahkan teks "Menunggu Pembayaran"
                if ($status === "pending") {
                    echo '<span class="status-badge ' . $statusClass . '">Lakukan Pembayaran</span>';
                } else {
                    echo '<span class="status-badge ' . $statusClass . '">' . htmlspecialchars($pesanan["status"]) . '</span>';
                }
                    ?>
                    
                </td>
            </tr>
            <tr><th>Tanggal</th><td><?= date("d-m-Y H:i", strtotime($pesanan["created_at"])); ?></td></tr>
        </table>

        <h4 class="mt-1 text-secondary">Detail Produk</h4>
        <div class="table-responsive">
            <table class="table table-bordered mt-2">
                <thead class="table-dark">
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_detail->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["nama_produk"]) ?></td>
                            <td>Rp.<?= number_format($row['harga'], 0, ',', '.'); ?>.-</td>
                            <td><?= htmlspecialchars($row["jumlah"]) ?> Pcs</td>
                            <td><strong>Rp.<?= number_format($row["subtotal"], 0, ',', '.') ?>.-</strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary btn-lg">Kembali</a>
        </div>
    </div>
</div>

<!-- TEMPLATE STRUK -->
<div id="struk" style="display: none;">
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        #struk-container {
            max-width: 350px;
            margin: auto;
            padding: 15px;
            border: 1px solid #ddd;
        }
        h3, h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        th {
            background-color: #f8f8f8;
        }
        .total {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
        }
        
        /* Mode Cetak */
        @media print {
            body { visibility: hidden; }
            #struk { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>


</body>
</html>