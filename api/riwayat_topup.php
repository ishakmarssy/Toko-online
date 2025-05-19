<?php
session_start();
include("../assets/kon.php");

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM topup WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Top-Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center">Riwayat Top-Up</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jumlah</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                <td><?= $row['metode']; ?></td>
                <td>
                    <?php if ($row['status'] == "pending"): ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php elseif ($row['status'] == "success"): ?>
                        <span class="badge bg-success">Berhasil</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Gagal</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
