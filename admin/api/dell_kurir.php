<?php
session_start();
include("../db/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];


if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $query = "DELETE FROM pengiriman WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Metode berhasil dihapus!'); window.location='../index.php#rekening';</script>";
    } else {
        echo "Gagal menghapus metode.";
    }
}
?>
