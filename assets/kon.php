<?php
//session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "toko_online";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
