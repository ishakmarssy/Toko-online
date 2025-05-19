<?php
//session_start();
include("../db/kon.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

// Ambil username admin yang login
$admin_username = $_SESSION["admin_username"];
$conn = new mysqli("localhost", "root", "", "toko_online");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT t.id, u.username, t.jumlah, t.metode, t.status, t.created_at
        FROM topup t
        JOIN users u ON t.user_id = u.id
        ORDER BY t.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Top-Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 60px;
        }
        .navbar-custom {
            background-color:rgb(255, 255, 255);
            transition: background 0.3s;
        }
        .navbar-custom .nav-link {
            color: black !important;
            font-weight: bold;
        }
        .navbar-custom .nav-link:hover {
            background: rgba(255, 255, 255, 0.27);
            border-radius: 5px;
        }
        .toggle-btn {
            display: none;
            background: #dc3545;
            color: white;
            border: none;
            font-size: 18px;
            padding: 8px 15px;
            border-radius: 5px;
            width: 100%;
            text-align: center;
        }
        @media (max-width: 991px) {
            .toggle-btn {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <!-- Tombol Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse position-relative" id="navbarNav">
            <!-- Tombol Tutup -->
            

            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#beranda"> <i class="bi bi-house"></i> Beranda</a>
                    <li class="nav-item">
                    <a class="nav-link" href="../index.php#pesanan"> <i class="bi bi-cart"></i> Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#produk"> <i class="bi bi-box"></i> Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#rekening"> <i class="bi bi-bank"></i> Rekening</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"> <i class="bi bi-list"></i> Permintaan TopUp</a>
                </li>
                <li class="nav-item text-danger">
                    <a class="nav-link " href="../logout.php"> <i class="bi bi-box-arrow-right"></i> Keluar</a>
            </ul>
        </div>
    </div>
</nav>
    <div class="container mt-4">
        <h4 class="text-center mb-3">Permintaan Top-Up</h4>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($row['username']) ?></h6>
                    <p class="mb-1"><strong>Jumlah:</strong> Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></p>
                    <p class="mb-1"><strong>Metode:</strong> <?= htmlspecialchars($row['metode']) ?></p>
                    <p class="mb-1"><strong>Tanggal:</strong> <?= $row['created_at'] ?></p>
                    <p class="mb-2"><strong>Status:</strong> 
                        <?php
                            $statusClass = "badge bg-secondary";
                            if ($row['status'] == 'pending') $statusClass = "badge bg-warning text-dark";
                            elseif ($row['status'] == 'success') $statusClass = "badge bg-success";
                            elseif ($row['status'] == 'failed') $statusClass = "badge bg-danger";
                        ?>
                        <span class="<?= $statusClass ?>"><?= ucfirst($row['status']) ?></span>
                    </p>
                    
                    <?php if ($row['status'] == 'pending') { ?>
                        <button class="btn btn-success btn-sm me-2" onclick="prosesTopup(<?= $row['id'] ?>, 'success')">
                            <i class="bi bi-check-circle"></i> Konfirmasi
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="prosesTopup(<?= $row['id'] ?>, 'failed')">
                            <i class="bi bi-x-circle"></i> Tolak
                        </button>
                    <?php } else { ?>
                        <span class="text-muted">Status: <?= ucfirst($row['status']) ?></span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <script>
        function prosesTopup(id, status) {
            if (confirm("Apakah Anda yakin ingin " + (status === 'success' ? "mengonfirmasi" : "menolak") + " top-up ini?")) {
                fetch("admin_proses_topup.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + id + "&status=" + status
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) window.location.reload();
                })
                .catch(error => console.error("Error:", error));
            }
        }
    </script>

<script>
    function closeNavbar() {
        var navbar = document.getElementById('navbarNav');
        var navbarCollapse = new bootstrap.Collapse(navbar, { toggle: false });
        navbarCollapse.hide();
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
