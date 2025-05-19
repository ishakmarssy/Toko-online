<?php
include '../assets/kon.php';
$query = "SELECT COUNT(*) as jumlah FROM pesanan WHERE status = 'Pending'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
echo $row['jumlah'];
?>