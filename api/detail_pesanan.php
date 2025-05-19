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
$query = "SELECT p.id, u.nama AS nama_user, p.hp_id, p.alamat, pe.nama AS metode_pengiriman,
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
        }
        .container {
            max-width: 900px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background: #ffffff;
        }
        .status-badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .status-diproses {
            background-color: #17a2b8;
            color: white; }
        .status-dikirim {
            background-color: #007bff;
            color: white; }
        .status-selesai {
            background-color: #28a745;
            color: white; }
        .status-gagal {
            background-color: #dc3545;
            color: white; }
        .status-dibatalkan {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4">
        <h3 class="text-center text-primary mb-4">Invoice <span>#<?= htmlspecialchars($pesanan["id"]) ?></span></h3>

        <table class="table">
            <tr><th>Nama</th><td>: <?= htmlspecialchars($pesanan["nama_user"]) ?></td></tr>
            <tr><th>Telp</th><td>: <?= htmlspecialchars($pesanan["hp_id"]) ?></td></tr>
            <tr><th>Alamat</th><td>: <?= htmlspecialchars($pesanan["alamat"]) ?></td></tr>
            <tr><th>Area</th><td>: <?= htmlspecialchars($pesanan["metode_pengiriman"]) ?></td></tr>
            <tr><th>Biaya</th><td>: <strong>Rp.<?= number_format($pesanan["biaya_pengiriman"], 0, ',', '.') ?>.-</strong></td></tr>
            <tr><th>Pembayaran</th><td>: <?= htmlspecialchars($pesanan["metode_pembayaran"]) ?></td></tr>
            <tr><th>Total</th><td>: <strong>Rp.<?= number_format($pesanan["total_harga"], 0, ',', '.') ?>.-</strong> (Sudah termasuk Ongkir)</td></tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                        $status = strtolower($pesanan["status"]);
                        $statusClass = "status-" . str_replace(" ", "", $status);
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($pesanan["status"]) ?></span>
                </td>
            </tr>
            <tr><th>Tanggal</th><td><?= date("d-m-Y H:i", strtotime($pesanan["created_at"])); ?></td></tr>
        </table>

        <h4 class="mt-4 text-secondary">Detail Produk</h4>
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
            <button class="btn btn-primary btn-lg" onclick="printStruk()">Print Struk</button>
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

    <div id="struk-container">
        <h3>Struk Pembelian</h3>
        <p><strong>Invoice:</strong> #<?= htmlspecialchars($pesanan["id"]) ?></p>
        <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan["nama_user"]) ?></p>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($pesanan["alamat"]) ?></p>
        <p><strong>Pengiriman:</strong> <?= htmlspecialchars($pesanan["metode_pengiriman"]) ?> (Rp.<?= number_format($pesanan["biaya_pengiriman"], 0, ',', '.') ?>)</p>
        
        <hr>
        
        <h4>Produk</h4>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jml</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result_detail->data_seek(0);
                while ($row = $result_detail->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["nama_produk"]) ?></td>
                        <td><?= htmlspecialchars($row["jumlah"]) ?></td>
                        <td>Rp.<?= number_format($row["subtotal"], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p class="total">Total: Rp.<?= number_format($pesanan["total_harga"], 0, ',', '.') ?></p>

        <p class="footer">Terima kasih telah berbelanja!</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function printStruk() {
    var struk = document.getElementById("struk").innerHTML;
    var win = window.open("", "", "width=400,height=600");
    win.document.write('<html><head><title>Struk</title></head><body>');
    win.document.write(struk);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>

</body>
</html>