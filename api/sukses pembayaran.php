<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../assets/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembayaran Berhasil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container text-center mt-5">
    <h2 class="text-success">Pembayaran Berhasil!</h2>
    <p>Terima kasih telah berbelanja. Pesanan Anda sedang diproses.</p>
    <a href="../index.php" class="btn btn-success">Kembali ke Beranda</a>
</div>
</body>
</html>
