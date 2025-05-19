<?php
session_start();
include("kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];

$query = "SELECT * FROM metode_pembayaran";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>CRUD Produk</title>
</head>
<body>
    <h2>Daftar Produk</h2>
    <a href="tambah.php">Tambah Produk</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Nomor Rekening</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= $row["nama"] ?></td>
            <td><?= $row["nomor_rekening"] ?></td>
            <td>
                <a href="edit.php?id=<?= $row["id"] ?>">Edit</a> | 
                <a href="hapus.php?id=<?= $row["id"] ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
