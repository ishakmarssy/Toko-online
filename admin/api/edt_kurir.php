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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $nama = $_POST["nama"];
    $biaya = $_POST["biaya"];

    $query = "UPDATE pengiriman SET nama = ?, biaya = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $nama, $biaya, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Metode berhasil diperbarui!'); window.location='../index.php#rekening';</script>";
    } else {
        echo "Gagal mengupdate metode.";
    }
}
