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
    $nama = $_POST["nama"];
    $nomor_rekening = $_POST["nomor_rekening"];

    $query = "INSERT INTO metode_pembayaran (nama, nomor_rekening) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nama, $nomor_rekening);
    
    if ($stmt->execute()) {
        echo "<script>alert('Metode berhasil ditambahkan!'); window.location='../index.php#rekening';</script>";
    } else {
        echo "Gagal menambahkan metode.";
    }
}
?>
