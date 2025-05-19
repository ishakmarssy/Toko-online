<?php
include("../assets/kon.php");

if (!isset($_GET['id'])) {
    echo "invalid";
    exit();
}

$topup_id = intval($_GET['id']);
$sql = "SELECT status FROM topup WHERE id = $topup_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    echo $row['status']; // Mengembalikan status (pending/success/failed)
} else {
    echo "invalid";
}
