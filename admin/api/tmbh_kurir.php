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
    $biaya = $_POST["biaya"];

    $query = "INSERT INTO pengiriman (nama, biaya) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nama, $biaya);
    
    if ($stmt->execute()) {
        echo "<script>alert('Metode berhasil ditambahkan!'); window.location='../index.php#rekening';</script>";
    } else {
        echo "Gagal menambahkan metode.";
    }
}
?>
