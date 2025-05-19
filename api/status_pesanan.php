<?php
//session_start(); // Pastikan sesi dimulai
include("../assets/kon.php");

// Ambil user_id dari sesi login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;


$user_id = $_SESSION['user_id'] ?? null;
$loggedIn = isset($_SESSION['user_id']);


if (!isset($_SESSION["user_id"])) {
    header("Location: ../assets/login.php");
    exit();
}

// Ambil waktu saat ini
$current_time = time();

// Periksa pesanan yang masih pending lebih dari 1 jam lalu ubah status menjadi "Gagal"
$query_update = "UPDATE pesanan SET status = 'Gagal' WHERE status = 'Pending' AND UNIX_TIMESTAMP(created_at) + (24 * 3600) < ?";
$stmt_update = $conn->prepare($query_update);
$stmt_update->bind_param("i", $current_time);
$stmt_update->execute();
$stmt_update->close();

// Query untuk mengambil nama toko
$sql = "SELECT store_name FROM store_settings WHERE id = 1";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_name = $row["store_name"] ?? $store_name;
    }
    $result->free();
}


$user_id = $_SESSION["user_id"];
$query = "SELECT *, UNIX_TIMESTAMP(created_at) AS created_at_unix FROM pesanan WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_pesanan = $stmt->get_result();
$stmt->close();
$status_badge = [
    "Pending" => "warning",
    "Diproses" => "info",
    "Dikirim" => "primary",
    "Selesai" => "success",
    "Gagal" => "danger",
    "Dibatalkan" => "danger"
];

$pesanan_per_status = [];
while ($row = $result_pesanan->fetch_assoc()) {
        // Hitung sisa waktu pembayaran jika pesanan masih pending
        if ($row["status"] === "Pending") {
            $sisa_waktu = max(0, ($row["created_at_unix"] + (24 * 3600)) - $current_time);
            $row["sisa_waktu"] = $sisa_waktu;
        }
        $pesanan_per_status[$row["status"]][] = $row;
    }
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style_index.css" />

    <style>
        :root {
                --warna-primer: #ee4d2d;
                --warna-sekunder: #c23616;
                --warna-teks: #333;
                --warna-habis: #ff3838;
                --warna-link: #f1c40f;
            }
        body {
            background-color: #f8f9fa;
            padding-bottom: 50px;
            padding-top: 70px;
            /*font-family: 'Shadows Into Light', cursive; /* Font tulisan tangan */
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            padding: 0px;
            font-size: 0.8rem;
            gap: 8px;
        }

        .card-title {
            font-size: 1rem;
        }
        /* Tambahkan CSS Kustom untuk Navbar -->*/
        .navbar-custom {
  background-color: #ffffff;
  border-bottom: 1px solid #dee2e6;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
  padding: 0.5rem 1rem;
  z-index: 1000;
  transition: all 0.3s ease;
}

.navbar-custom .navbar-brand {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--warna-primer);
  display: flex;
  align-items: center;
  gap: 4px;
  text-decoration: none;
}

.navbar-custom .navbar-brand i {
  font-size: 1.25rem;
  color: var(--warna-primer);
  transition: color 0.2s ease;
}

.navbar-custom .navbar-brand:hover i {
  color: #023e8a;
}

.navbar-custom .navbar-brand:hover {
  color: #0077b6;
}

/* Responsive tweak */
@media (max-width: 576px) {
  .navbar-custom .navbar-brand {
    font-size: 1rem;
  }

  .navbar-custom .navbar-brand i {
    font-size: 1.15rem;
  }
}

        /* Tab Navigasi */
.nav-tabs {
  border-bottom: 2px solid #dee2e6;
}

.nav-tabs .nav-link {
  border: none;
  border-bottom: 3px solid transparent;
  font-weight: 500;
  color: #6c757d;
  transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
  color: var(--warna-primer);
  background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
  border-color: var(--warna-primer);
  color: var(--warna-primer);
  background-color: #fff;
}

/* Kartu Pesanan */
.card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.03);
  transition: transform 0.2s ease;
  margin-bottom: 1rem;
}

.card:hover {
  transform: scale(1.01);
}

/* Judul Invoice */
.card .invoice {
  font-weight: 600;
  color: #495057;
}

/* Badge Status */
.badge {
  font-size: 0.85rem;
  padding: 0.4em 0.6em;
  border-radius: 8px;
}

/* Tombol Aksi */
.batalPesanan, .selesai-btn {
  margin-top: 0.5rem;
  font-size: 0.85rem;
  padding: 5px 12px;
  border-radius: 8px;
  transition: background-color 0.2s ease;
}

.batalPesanan:hover {
  background-color: #c82333;
  color: white;
}

.selesai-btn:hover {
  background-color: #218838;
  color: white;
}

/* Countdown */
.card-text.text-danger {
  font-weight: 500;
  font-size: 0.9rem;
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .card .invoice {
    font-size: 1rem;
  }

  .batalPesanan, .selesai-btn {
    width: 100%;
  }
}


        .swal-kecil {
            font-size: 14px;
            padding: 10px;
            background: #fff; /* Latar belakang putih */
            border: 2px solid #000; /* Border hitam untuk efek sketsa */
            box-shadow: 3px 3px 0px #000; /* Efek sketsa bayangan */
            /*filter: grayscale(100%) contrast(1.2); /* Efek hitam-putih */
            border-radius: 5px; /* Sudut sedikit melengkung */
        }
        /* Animasi Pulse untuk Status Pending */
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        /* Status Pending */
        .status-pending {
            background-color: #ffc107;
            color: black;
            animation: pulse 1.5s infinite;
        }

        
    </style>

    <style>
        @media (max-width: 576px) {

            body {
                background-color: #f8f9fa;
                padding-bottom: 70px;
                padding-top: 70px;
                /*font-family: 'Shadows Into Light', cursive; /* Font tulisan tangan */
            }

            

            .swal-kecil {
                font-size: 14px;
                padding: 10px;
                background: #fff; /* Latar belakang putih */
                border: 2px solid #000; /* Border hitam untuk efek sketsa */
                box-shadow: 3px 3px 0px #000; /* Efek sketsa bayangan */
                /*filter: grayscale(100%) contrast(1.2); /* Efek hitam-putih */
                border-radius: 5px; /* Sudut sedikit melengkung */
            }
            /* Animasi Pulse untuk Status Pending */
            @keyframes pulse {
                0% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.1); opacity: 0.8; }
                100% { transform: scale(1); opacity: 1; }
            }
            /* Status Pending */
            .status-pending {
                background-color: #ffc107;
                color: black;
                animation: pulse 1.5s infinite;
            }
        }
        /* Background navbar Navbar Bawah */
        .navbar.bg-warna {
        background-color: #fff;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.15);
        padding: 0.3rem 0;
        z-index: 1100;
        }

        /* Container flex */
        .navbar .container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        }

        /* Item nav link */
        .navbar .nav-link {
        color: var(--warna-primer);
        font-size: 0.85rem;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
        user-select: none;
        }

        /* Flex vertical for icon + text */
        .nav-item-flex {
        gap: 4px;
        }

        /* Icon style */
        .nav-link i.material-icons {
        font-size: 28px;
        line-height: 1;
        transition: transform 0.3s ease;
        }

        /* Text below icon */
        .nav-link span {
        font-weight: 600;
        letter-spacing: 0.02em;
        }

        /* Hover effect */
        .nav-link:hover {
        color: var(--warna-primer);
        }

        /* Active nav item style */
        .nav-link.active,
        .nav-link.active:hover {
        color: var(--warna-primer);
        }

        .nav-link.active i.material-icons {
        transform: scale(1.15);
        }

        /* Responsive: smaller screens */
        @media (max-width: 480px) {
        .navbar.bg-warna {
            padding: 0.2rem 0;
        }
        .nav-link i.material-icons {
            font-size: 24px;
        }
        .nav-link span {
            font-size: 0.75rem;
        }
        }
        /* Custom Offcanvas Style */
.custom-offcanvas {
  width: 250px;
  background-color: #fff;
  border-right: 1px solid #e5e5e5;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
}

/* Header Style */
.custom-offcanvas .offcanvas-header {
  border-bottom: 1px solid #f0f0f0;
  padding: 1rem 1.2rem;
}

.custom-offcanvas .offcanvas-title {
  font-size: 1.1rem;
  font-weight: bold;
  color: #ee4d2d;
}

/* List Group Link Style */
.custom-offcanvas .nav-link {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  font-size: 15px;
  color: #333;
  text-decoration: none;
  border-bottom: 1px solid #f1f1f1;
  transition: background 0.3s ease;
}

.custom-offcanvas .nav-link:hover {
  background-color: #fdf2ef;
  color: #ee4d2d;
}

/* Icon Style */
.custom-offcanvas .oc-icon,
.custom-offcanvas .material-icons {
  margin-right: 10px;
  font-size: 1.3rem;
  color: #ee4d2d;
}

/* Logout special color */
.custom-offcanvas .nav-link.text-danger {
  color: #d9534f;
}

.custom-offcanvas .nav-link.text-danger:hover {
  background-color: #fdecea;
  color: #c9302c;
}

/* Button login & register */
.custom-offcanvas .btn {
  font-size: 14px;
  padding: 8px 16px;
}

@media (max-width: 576px) {
  .custom-offcanvas {
    width: 100%;
  }
}

    </style>

</head>

<body>
    
<!-- Offcanvas -->
<div class="offcanvas offcanvas-start  custom-offcanvas"  tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel"><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i></h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
    <div class="offcanvas-body">
      <div class="list-group">
        <!-- Bagian kanan: Link Navigasi -->
        <?php if ($loggedIn): ?>
          <a class="nav-link" href="../api/status_pesanan.php">
          <i class="material-icons oc-icon">history</i> Status Pesanan
          </a>
          <a class="nav-link" href="../assets/keranjang.php">
          <i class="material-icons oc-icon">shopping_cart</i> Keranjang
          </a>
          <a class="nav-link" href="../perbaikan.html">
          <i class="material-icons oc-icon">settings</i> Pengaturan
          </a>
          <a class="nav-link" href="../assets/profil.php">
          <i class="material-icons oc-icon">account_circle</i> Profil
          </a>
          <a class="nav-link text-danger" href="../assets/logout.php">
            <i class="bi bi-box-arrow-right oc-icon" style="font-size: 1.5rem;"></i> Keluar
          </a>
          <?php else: ?>
          <div class="d-flex align-items-center">
            <a class="btn btn-outline-success me-2" href="../assets/login.php"><i class="bi bi-box-arrow-in-right"></i> Masuk</a>
            <a class="btn btn-success" href="../assets/register.php">Daftar</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
</div>

<?php //session_start(); ?>
<?php if (isset($_SESSION["pesan_sukses"])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Pesanan Berhasil!',
            text: "<?= $_SESSION['pesan_sukses']; ?>",
            confirmButtonColor: '#28a745',
            confirmButtonText: 'OK'
        });
    </script>
    <?php unset($_SESSION["pesan_sukses"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["pesan_error"])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "<?= $_SESSION['pesan_error']; ?>",
            confirmButtonColor: '#d33',
            confirmButtonText: 'Coba Lagi'
        });
    </script>
    <?php unset($_SESSION["pesan_error"]); ?>
<?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-light navbar-custom fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="javascript:history.back()" class="navbar-brand"><i class="bi bi-arrow-left"></i></a>
            <a class="navbar-brand"  href="../index.php"><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i></a>
            <a href="../assets/keranjang.php" class="navbar-brand"><i class="bi bi-cart3"></i></a>
        </div>
    </nav>

    <div class="container mt-0">
        <h2 class="text-center">Status Pesanan</h2>
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="pesananTabs" role="tablist">
            <?php foreach ($status_badge as $status => $badge): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php if ($status === 'Pending') echo 'active'; ?>" id="<?= strtolower($status); ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= strtolower($status); ?>" type="button" role="tab">
                        <?= $status; ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content mt-3" id="pesananTabsContent">
            <?php foreach ($status_badge as $status => $badge): ?>
                <div class="tab-pane fade <?php if ($status === 'Pending') echo 'show active'; ?>" id="<?= strtolower($status); ?>" role="tabpanel">
                    <div class="row">
                        <?php if (!empty($pesanan_per_status[$status])): ?>
                            <?php foreach ($pesanan_per_status[$status] as $row): ?>
                                <div class="col-md-6 col-lg-4">
                                    <!-- Card Container -->
                                    <div class="card" style="cursor: pointer;" onclick="window.location.href='detail_pesanan_user.php?id=<?= $row["id"]; ?>'">
                                        <div class="card-body">
                                            <h5 class="card-title text-end invoice">Invoice #<?= $row["id"]; ?></h5>
                                            <p class="card-text mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($row["alamat"]); ?></p>
                                            <p class="card-text mb-1"><strong>Rekening:</strong> <code class="norek-item"><?= htmlspecialchars($row["nomor_rekening"]); ?></code></p>
                                            <p class="card-text mb-1"><strong>Total Harga + Ongkir:</strong> Rp.<?= number_format($row["total_harga"], 0, ',', '.'); ?>,-</p>
                                            <p class="card-text mb-1"><strong>Status Pesanan:</strong>
                                                <span class="badge bg-<?= $status_badge[$row["status"]] ?? "secondary"; ?>">
                                                    <?= htmlspecialchars($row["status"]); ?>
                                                </span>
                                            </p>
                                            <p class="card-text text-end"><small class="text-muted">Tanggal: <?= date("d-m-Y H:i", strtotime($row["created_at"])); ?></small></p>

                                            <!-- Countdown Timer jika pesanan masih pending -->
                                            <?php if ($row["status"] == "Pending" && isset($row["sisa_waktu"])): ?>
                                                <p class="card-text text-danger">Bayar sebelum: <span id="countdown-<?= $row["id"]; ?>"></span></p>
                                                <script>
                                                    var waktuSisa<?= $row["id"]; ?> = <?= $row["sisa_waktu"]; ?>;
                                                    function updateCountdown<?= $row["id"]; ?>() {
                                                        var jam = Math.floor(waktuSisa<?= $row["id"]; ?> / 3600);
                                                        var menit = Math.floor((waktuSisa<?= $row["id"]; ?> % 3600) / 60);
                                                        var detik = waktuSisa<?= $row["id"]; ?> % 60;
                                                        document.getElementById("countdown-<?= $row["id"]; ?>").innerText = jam + "j " + menit + "m " + detik + "d";

                                                        if (waktuSisa<?= $row["id"]; ?> > 0) {
                                                            waktuSisa<?= $row["id"]; ?>--;
                                                            setTimeout(updateCountdown<?= $row["id"]; ?>, 1000);
                                                        } else {
                                                            document.getElementById("countdown-<?= $row["id"]; ?>").innerText = "Waktu Habis";
                                                        }
                                                    }
                                                    updateCountdown<?= $row["id"]; ?>();
                                                </script>

                                                <!-- Tombol Batalkan Pesanan -->
                                                <form id="batalPesananForm_<?= $row['id']; ?>" action="batal_pesanan.php" method="POST">
                                                    <input type="hidden" name="id_pesanan" value="<?= $row['id']; ?>">
                                                    <button type="button" class="btn btn-danger btn-sm batalPesanan" data-id="<?= $row['id']; ?>" onclick="event.stopPropagation();">
                                                        Batalkan Pesanan
                                                    </button>
                                                </form>
                                            <?php elseif ($row["status"] == "Dikirim"): ?>
                                                <!-- Tombol Selesaikan Pesanan -->
                                                <form id="selesaiForm-<?= $row["id"]; ?>" action="selesai_pesanan.php" method="POST">
                                                    <input type="hidden" name="id_pesanan" value="<?= $row["id"]; ?>">
                                                    <input type="hidden" name="status" value="Selesai">
                                                    <button type="button" class="btn btn-success btn-sm selesai-btn" data-id="<?= $row["id"]; ?>" onclick="event.stopPropagation();">
                                                        Selesaikan Pesanan
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">Tidak ada pesanan kamu dengan status "<?= $status; ?>".</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Navigasi Bawah -->
    <?php
    if (isset($_SESSION['user_id'])) {
    ?>
        <!-- Navbar Bawah (Hanya Jika Sudah Login) -->
        <<nav class="navbar bg-warna fixed-bottom shadow navbar-bottom">
    <div class="container d-flex justify-content-around">
        <a href="../index.php" class="nav-link text-center ">
            <i class="material-icons">home</i>
            <span>Home</span>
        </a>
        <a href="#" class="nav-link text-center  active">
            <i class="material-icons">history</i>
            <span>Riwayat</span>
        </a>
        <a data-bs-toggle="offcanvas"
           data-bs-target="#offcanvasWithBothOptions"
           aria-controls="offcanvasWithBothOptions"
           href="#"
           class="nav-link text-center ">
            <i class="material-icons">dashboard</i>
            <span>Menu</span>
        </a>
        <a href="../assets/profil.php" class="nav-link text-center ">
            <i class="material-icons">account_circle</i>
            <span>Akun</span>
                <!--<a  data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBothOptions"
                    aria-controls="offcanvasWithBothOptions"
                    href="#"
                    class="nav-link text-center ">
                    <i class="material-icons">dashboard</i><br><span>Menu</span>
                </a>-->
            </div>
        </nav>
    <?php
    }
    ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".batalPesanan").forEach(button => {
        button.addEventListener("click", function() {
            let idPesanan = this.getAttribute("data-id");

            Swal.fire({
                title: "Yakin ingin membatalkan pesanan?",
                text: "Pesanan yang dibatalkan tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Batalkan!",
                cancelButtonText: "Batal",
                width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim form berdasarkan ID pesanan yang diklik
                    let form = document.getElementById("batalPesananForm_" + idPesanan);
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    });
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk mendeteksi koneksi internet
        function checkConnection() {
            if (!navigator.onLine) {
                // Jika koneksi terputus, alihkan ke offline.php
                window.location.href = '../offline.php';
            }
        }

        // Cek koneksi saat halaman dimuat
        window.addEventListener('load', checkConnection);

        // Cek koneksi saat status berubah (offline)
        window.addEventListener('offline', () => {
            window.location.href = '../offline.php';
        });
    </script>

    <!-- Tambahkan SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".selesai-btn").forEach(button => {
        button.addEventListener("click", function() {
            let id = this.getAttribute("data-id");
            Swal.fire({
                title: "Tunggu Dulu!",
                text: "Yakin ingin menyelesaikan pesanan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Selesaikan!",
                cancelButtonText: "Batal",
                width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Pesanan telah diselesaikan.",
                        icon: "success",
                        timer: 2000,  // ✅ Menutup otomatis setelah 2 detik
                        showConfirmButton: false,
                        width: "300px",
                        customClass: {
                            popup: "swal-kecil"
                        }
                    });

                    // Submit formulir setelah konfirmasi
                    setTimeout(() => {
                        document.getElementById("selesaiForm-" + id).submit();
                    }, 2000); // ✅ Tunggu 2 detik sebelum submit
                }
            });
        });
    });
});

</script>

<?php if (isset($_SESSION['alert'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: "<?= $_SESSION['alert']['type']; ?>",
            title: "<?= $_SESSION['alert']['message']; ?>",
            showConfirmButton: false,
            timer: 2000,
            width: "300px",
                        customClass: {
                            popup: "swal-kecil"
                        }
        });
    </script>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>


</body>

</html>