<?php
include ("kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = md5($_POST["password"]);
    $role = $_POST["role"];

    if ($role == "admin") {
        $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    } else {
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $role;
        if ($role == "admin") {
            header("Location: ../admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "<script>alert('Login gagal! Periksa kembali username dan password.'); window.location='../admin/login.php';</script>";
    }
}


// Tentukan jumlah data per halaman
$limit = 10;
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$offset = ($page - 1) * $limit;

/// Ambil data pesanan dengan batasan halaman
$sql = "SELECT pembayaran.id, users.username, produk.nama, pembayaran.jumlah, pembayaran.total_harga, pembayaran.status 
FROM pembayaran
JOIN users ON pembayaran.user_id = users.id
JOIN produk ON pembayaran.produk_id = produk.id
ORDER BY pembayaran.id DESC

LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Hitung total pesanan untuk pagination
$sql_total = "SELECT COUNT(*) AS total FROM pembayaran";
$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_pesanan = $total_row["total"];
$total_pages = ceil($total_pesanan / $limit);
?>
