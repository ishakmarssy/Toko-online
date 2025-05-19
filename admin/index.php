<?php
//session_start();
include("db/kon.php");
//include("../assets/function.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin/login.php");
    exit();
}

// (Pastikan validasi session admin dilakukan agar hanya admin yang dapat mengakses halaman ini)

$error = '';
$success = '';
$error_store = '';
$success_store = '';

// Ambil data Nomor WhatsApp dari database
$query_wa = "SELECT no_wa FROM admin LIMIT 1";
$result_wa = $conn->query($query_wa);
$admin_data = $result_wa->fetch_assoc();

// Proses update Nomor WhatsApp
if (isset($_POST['submit_wa'])) {
    $no_wa = trim($_POST['no_wa']);
  
    // Validasi format nomor WhatsApp (hanya angka, boleh dengan kode negara)
    if (!preg_match('/^\+?[0-9]+$/', $no_wa)) {
        $error_wa = "Format nomor WhatsApp tidak valid!";
    } elseif (empty($no_wa)) {
        $error_wa = "Nomor WhatsApp tidak boleh kosong!";
    } else {
        // Update nomor WhatsApp ke database
        $update_query_wa = "UPDATE admin SET no_wa = ? LIMIT 1";
        $stmt_wa = $conn->prepare($update_query_wa);
        $stmt_wa->bind_param("s", $no_wa);
  
        if ($stmt_wa->execute()) {
            // Reload halaman setelah update sukses
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error_wa = "Terjadi kesalahan, coba lagi!";
        }
        $stmt_wa->close();
    }
  }

  // Query untuk mengambil nama toko
$sql = "SELECT store_name FROM store_settings WHERE id = 1";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_name = $row["store_name"] ?? $store_name;
    }
    $result->free();
}


  // Ambil data pengaturan nama toko dari database
$sql_store = "SELECT * FROM store_settings WHERE id = 1";
$result_store = $conn->query($sql_store);
if ($result_store && $result_store->num_rows > 0) {
    $store_settings = $result_store->fetch_assoc();
    
} else {
    $store_settings = [
        'store_name' => 'Nama Toko Default'
    ];
}


// Proses update pengaturan nama toko jika form disubmit
if (isset($_POST['submit_store'])) {
    $store_name = $_POST['store_name'] ?? '';
    $sql_update_store = "UPDATE store_settings SET store_name = ? WHERE id = 1";
    if ($stmt_store = $conn->prepare($sql_update_store)) {
        $stmt_store->bind_param("s", $store_name);
        if ($stmt_store->execute()) {
            $success_store = "Nama toko berhasil diperbarui.";
            $result_store = $conn->query("SELECT * FROM store_settings WHERE id = 1");
            if ($result_store && $result_store->num_rows > 0) {
                $store_settings = $result_store->fetch_assoc();
            }
        } else {
            $error_store = "Gagal memperbarui nama toko.";
        }
        $stmt_store->close();
    } else {
        $error_store = "Gagal mempersiapkan query untuk nama toko.";
    }
}

$admin_id = $_SESSION["admin_id"];
$query_admin = "SELECT * FROM admin WHERE id = '$admin_id'";
$result_admin = $conn->query($query_admin);
$data_admin = $result_admin->fetch_assoc();

$sql = "SELECT * FROM pengiriman ORDER BY id DESC";
$result = $conn->query($sql);

$admin_username = $_SESSION["admin_username"];

$query = "SELECT p.id, u.nama AS nama_user, p.alamat, pe.nama AS metode_pengiriman, m.nama AS metode_pembayaran, p.total_harga, p.status, p.created_at 
          FROM pesanan p
          JOIN users u ON p.user_id = u.id
          JOIN pengiriman pe ON p.pengiriman_id = pe.id
          JOIN metode_pembayaran m ON p.metode_pembayaran_id = m.id
          ORDER BY p.created_at DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            margin: 0;
        }

        /* Sidebar */
        #sidebar {
            margin-top: 70px;
            min-width: 260px;
            max-width: 260px;
            background: #093028;  /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #237A57, #093028);  /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #237A57, #093028); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            color: white;
            height: 100vh;
            position: fixed;
            transition: all 0.3s ease-in-out;
            padding-top: 20px;
            box-shadow: 3px 0px 10px rgba(20, 163, 192, 0.59);
        }

        /* Link Sidebar */
        #sidebar .nav-link {
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin: 5px;
            transition: background 0.3s ease-in-out, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        #sidebar .nav-link.active {
            background: #0d6efd;
        }

        /* Konten */
        #content {
            margin-top: 0px;
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
            padding-top: 70px;  /* Tambahkan ruang untuk navbar tetap terlihat */
        }

        /* Ketika sidebar disembunyikan */
        .sidebar-collapsed #content {
            margin-left: 0;
            padding: 20px;
            width: 100%;
            max-width: 100%;
            display: flex;
            justify-content: center;
            padding-top: 70px;  /* Jaga jarak dari navbar */
        }

        /* Sidebar Collapsed */
        .sidebar-collapsed #sidebar {
            min-width: 60px;
            max-width: 60px;
            text-align: center;
        }

        .sidebar-collapsed .nav-link {
            justify-content: center;
        }

        .sidebar-collapsed .nav-link span {
            display: none;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;  /* Pastikan navbar berada di atas konten */
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Card */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        /* Responsif untuk perangkat mobile */
        @media (max-width: 768px) {
            /* Sidebar pada layar kecil */
            #sidebar {
                min-width: 200px;
                max-width: 200px;
            }

            /* Navbar tetap di atas */
            .navbar {
                padding: 10px 15px;
            }
        }

    </style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
    <div class="container-fluid">
        <button class="btn btn-dark" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <a class="navbar-brand ms-3" href="index.php">
        <?php echo htmlspecialchars($store_name); ?>
        <i class="bi bi-bag-check-fill"></i>
      </a>
        <!-- Bagian Kanan Navbar -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-5 me-1"></i> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item mb-2" href="#" data-bs-toggle="modal" data-bs-target="#profilModal"><i class="bi bi-person"></i> Profil</a></li>
                <li><a class="dropdown-item mb-2" href="#" data-bs-toggle="modal" data-bs-target="#modalNamaToko"><i class="bi bi-shop"></i> Nama Toko</a></li>
                <li><a class="dropdown-item mb-2" href="#" data-bs-toggle="modal" data-bs-target="#pengaturanWAModal"><i class="bi bi-whatsapp"></i> Ubah No. WA</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

    <nav id="sidebar" class="d-flex flex-column p-3">
        <h4 class="text-center">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#home"><i class="bi bi-house"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pesanan"><i class="bi bi-cart"></i> Pesanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#produk"><i class="bi bi-box"></i> Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#rekening"><i class="bi bi-bank"></i> Rekening</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#slider"><i class="bi bi-aspect-ratio"></i> Iklan Slide</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="api/admin_settings.php"><i class="bi bi-gear"></i> Pengaturan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </li>
        </ul>
    </nav>
        
   



    <div class="content" id="content">
        <div class="container">
            <?php
            include("modal/admin_profile.php");
            include("api/nomor_wa.php");
            include("api/nama_toko.php");
            include("../assets/sshome.php");
            //include("../assets/ss1.php");
            include("../assets/ss2.php");
            include("../assets/ss3.php");
            include("../assets/ss4.php");
            
            ?>
        </div>
    </div>
<!-- Navbar Bawah 
<nav class="navbar fixed-bottom navbar-light justify-content-around">
    <a class="nav-link text-center text-light" href="#home">
        <i class="bi bi-house fs-4"></i><br>Home
    </a>
    <a class="nav-link text-center text-light" href="#pesanan">
        <i class="bi bi-cart fs-4"></i><br>Pesanan
    </a>
    <a class="nav-link text-center text-light" href="#produk">
        <i class="bi bi-box fs-4"></i><br>Produk
    </a>
</nav> -->

<!-- Floating Button with Menu
<div class="position-fixed bottom-0 end-0 m-3">
    <div class="floating-menu" id="floatingMenu">
        <a class="btn" href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
        <a class="btn" href="index.php"><i class="bi bi-arrow-clockwise"></i></a>
        <a class="btn" href="api/admin_settings.php"><i class="bi bi-gear"></i></a>
        <button type="button" class="btn btn-sm btn-success bi-plus-circle mb-2" data-bs-toggle="modal" data-bs-target="#inputProdukModal">
            </button>
            
    </div>
    <button class="btn shadow-lg rounded-circle" id="floatingButton">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
</div> -->

<script>
        document.getElementById("sidebarToggle").addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("d-none");
        });
    </script>

<script>
    document.getElementById('floatingButton').addEventListener('click', function() {
        let menu = document.getElementById('floatingMenu');
        if (menu.style.display === 'flex') {
            menu.style.display = 'none';
        } else {
            menu.style.display = 'flex';
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
    let audio = new Audio('assets/sounds/notif3.wav');
    let lastOrderCount = 0; // Simpan jumlah pesanan sebelumnya

    function checkNewOrders() {
        $.ajax({
            url: '../api/cek_pesanan_baru.php',
            type: 'GET',
            success: function(response) {
                let jumlahPesanan = parseInt(response) || 0;
                let existingNotification = $("#orderNotification");

                if (jumlahPesanan > 0) {
                    if (existingNotification.length === 0) {
                        // Jika belum ada notifikasi, buat yang baru
                        $("body").append(`
                            <div id="orderNotification" class="alert alert-danger position-fixed top-0 end-0 m-3" style="z-index: 1050;">
                                Ada <span id="jumlahPesanan">${jumlahPesanan}</span> pesanan baru!
                                <button class="btn btn-sm btn-light ms-2" id="reloadPesanan">Lihat</button>
                            </div>
                        `);
                        audio.play().catch(error => console.log("Audio gagal diputar:", error));
                    } else {
                        // Jika sudah ada notifikasi, perbarui jumlahnya
                        let prevJumlah = parseInt($("#jumlahPesanan").text()) || 0;
                        $("#jumlahPesanan").text(jumlahPesanan);

                        // Jika ada tambahan pesanan, bunyikan notifikasi
                        if (jumlahPesanan > lastOrderCount) {
                            audio.play().catch(error => console.log("Audio gagal diputar:", error));
                        }
                    }
                } else {
                    existingNotification.remove();
                }

                lastOrderCount = jumlahPesanan; // Simpan jumlah terbaru
            },
            error: function(xhr, status, error) {
                console.error("Gagal memeriksa pesanan baru:", error);
            }
        });
    }

    $(document).on("click", "#reloadPesanan", function() {
        let url = "index.php#pesanan";
        if (window.location.href.includes("#pesanan")) {
            location.reload();
        } else {
            location.href = url;
            setTimeout(() => location.reload(), 100);
        }
    });

    setInterval(checkNewOrders, 5000);
});

</script>



    <script>
        function closeNavbar() {
            var navbar = document.getElementById('navbarNav');
            var navbarCollapse = new bootstrap.Collapse(navbar, {
                toggle: false
            });
            navbarCollapse.hide();
        }
    </script>

    <script>
        $(document).ready(function() {
            // Mengisi modal edit
            $(".editKurir").on("click", function() {
                var id = $(this).data("id");
                var nama = $(this).data("nama");
                var biaya = $(this).data("biaya");

                $("#edit-idk").val(id);
                $("#edit-namak").val(nama);
                $("#edit-biaya").val(biaya);

                $("#editKurModal").modal("show");
            });

            // Konfirmasi hapus
            $(".deleteKurir").on("click", function() {
                var id = $(this).data("id");

                if (confirm("Apakah Anda yakin ingin menghapus jasa pengantaran ini?")) {
                    $.ajax({
                        url: "../admin/api/hapus_kurir.php",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Gagal menghapus data!");
                        }
                    });
                }
            });
        });
        
        // Menambahkan event listener pada tombol toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    // Menambah atau menghapus class 'sidebar-collapsed' pada body atau elemen tertentu
    document.body.classList.toggle('sidebar-collapsed');
});

    </script>

    
</body>

</html>