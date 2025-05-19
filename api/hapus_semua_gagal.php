<?php
include("../assets/kon.php"); // Pastikan file koneksi database benar

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "DELETE FROM pesanan WHERE status = 'Gagal'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }

    $conn->close();
}
