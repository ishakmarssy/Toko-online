<?php
//session_start();
include("assets/kon.php");


$user_id = $_SESSION['user_id'] ?? null;
$loggedIn = isset($_SESSION['user_id']);

// Jika user sudah login, ambil data saldo dari tabel users
if ($loggedIn) {
    $query = "SELECT saldo FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $saldo = $user['saldo'] ?? 0;
    $stmt->close();
}

// Inisialisasi nilai default
$store_name = "Nama Toko Default";

// Query untuk mengambil nama toko
$sql = "SELECT store_name FROM store_settings WHERE id = 1";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_name = $row["store_name"] ?? $store_name;
    }
    $result->free();
}

// Ambil user_id dari sesi login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Jika user sudah login, hitung jumlah produk dalam keranjang
$jumlah_keranjang = 0;
if ($user_id > 0) {
    $sql_keranjang = "SELECT SUM(jumlah) AS jumlah FROM keranjang WHERE user_id = $user_id";
    $result_keranjang = $conn->query($sql_keranjang);
    $data_keranjang = $result_keranjang->fetch_assoc();
    $jumlah_keranjang = ($data_keranjang["jumlah"] !== null) ? $data_keranjang["jumlah"] : 0;
} else {
    $jumlah_keranjang = 0;
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Beranda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Menggunakan font Roboto untuk nuansa modern -->
     <!-- CSS -->
    <link rel="stylesheet" href="css/style_index.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        :root {
                --warna-primer: #ee4d2d;
                --warna-sekunder: #c23616;
                --warna-teks: #333;
                --warna-habis: #ff3838;
                --warna-link: #f1c40f;
            }

        body {
                background-color:rgb(246, 246, 246); /* Latar belakang putih seperti kertas */
                margin-bottom: 75px;
                padding-top: 70px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       /* ===== Global Navbar Styling ===== */

        .navbar-custom {
        background: var(--warna-primer);
        padding: 12px 0;
        z-index: 1000;
        }

        .navbar .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        }

        /* Search Form Styling */
        .search-wrapper {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.15);
        }

        .search-wrapper input[type="search"] {
        border: none;
        padding: 10px 15px;
        font-size: 1rem;
        }

        .search-wrapper input:focus {
        outline: none;
        box-shadow: none;
        }

        .btn-cari {
        background-color: #fff;
        color: #007bff;
        border: none;
        padding: 0 16px;
        transition: all 0.3s ease;
        }

        .btn-cari:hover {
        background-color: #f8f9fa;
        color: #0056b3;
        }

        /* Cart Icon Styling */
        .navbar .navbar-brand {
        position: relative;
        color: #fff;
        text-decoration: none;
        font-size: 1.25rem;
        }

        .navbar .navbar-brand i {
        font-size: 1.5rem;
        }

        .navbar .navbar-brand:hover {
        color: #f8f9fa;
        }

        /* Cart Count Badge */
        #cart-count {
        background-color: #dc3545;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: bold;
        top: -5px;
        left: 70%;
        box-shadow: 0 0 0 2px white;
        }

        /* Sticky navbar on scroll */
        .navbar-custom.fixed-top {
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }


        /* ===================== */
        /* KARTU PRODUK UMUM */
        /* ===================== */
        .card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease;
        }

        .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
        height: 200px;
        object-fit: cover;
        }

        .card-title {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
        }

        .card-text {
        font-size: 0.8rem;
        }

        .card-desk {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.4rem;
        }

        /* ===================== */
        /* HARGA LAMA (DISKON) */
        /* ===================== */
        .harga-lama {
        font-size: 0.85rem;
        color: #999;
        margin-right: 5px;
        }

        /* ===================== */
        /* STOK HABIS */
        /* ===================== */
        .habis {
        opacity: 0.3;
        filter: grayscale(100%);
        }

        .stok-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background:rgb(255, 0, 25);
        color: #fff;
        padding: 3px 8px;
        font-size: 0.75rem;
        font-weight: bold;
        border-radius: 6px;
        z-index: 2;
        }

        /* ===================== */
        /* PROMO OVERLAY */
        /* ===================== */
        .promo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        height: 60px;
        width: 60px;
        z-index: 3;
        }

        /* ===================== */
        /* DISABLED LINK (JIKA HABIS) */
        /* ===================== */
        .disabled-link {
        pointer-events: none;
        opacity: 0.9;
        }

        /* ===================== */
        /* BUTTON TAMBAH KE KERANJANG */
        /* ===================== */
        .btn-toko {
        background-color: var(--warna-primer);
        color: white;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        justify-content: center;
        align-items: center;
        }

        .btn-toko:hover {
        background-color: #218838;
        color: white;
        }

        /* ===================== */
        /* MODAL TAMBAH KE KERANJANG */
        /* ===================== */
        .modal-content {
        border-radius: 16px;
        }

        .modal-body img {
        max-width: 200px;
        height: auto;
        margin-bottom: 1rem;
        border-radius: 10px;
        }

        .form-keranjang .btn {
        min-width: 40px;
        height: 40px;
        font-size: 1rem;
        }

        .jumlah-text {
        min-width: 40px;
        text-align: center;
        }

        .btn-close-black {
        filter: brightness(0);
        }

        /* ===================== */
        /* RESPONSIVE (Mobile) */
        /* ===================== */
        @media (max-width: 768px) {
        .card-img-top {
            height: 160px;
        }

        .modal-body img {
            max-width: 100%;
        }

        .btn-toko {
            width: 34px;
            height: 34px;
        }
        }


        /* Container carousel */
        .carousel-container {
        max-width: 900px;
        margin: 2rem auto 3rem auto;
        padding: 0 15px;
        }

        /* Carousel item gambar */
        .carousel-item img {
        border-radius: 15px;
        max-height: 400px;
        object-fit: cover;
        transition: transform 0.4s ease;
        cursor: pointer;
        }

        /* Zoom efek saat hover gambar */
        .carousel-item img:hover {
        transform: scale(1.05);
        }

        /* Kontrol carousel (panah prev/next) */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
        filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.3));
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        width: 45px;
        height: 45px;
        background-size: 25px 25px;
        }

        /* Tombol carousel posisi */
        .carousel-control-prev,
        .carousel-control-next {
        width: 50px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.9;
        transition: opacity 0.3s ease;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
        opacity: 1;
        }

        /* Text ketika tidak ada slide */
        .carousel-item .text-center {
        font-size: 1.2rem;
        color: #666;
        padding: 80px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
        .carousel-container {
            max-width: 100%;
            margin: 1rem auto;
        }

        .carousel-item img {
            max-height: 250px;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 35px;
            height: 35px;
            background-size: 20px 20px;
        }
        }


        .alert-custom {
        border-radius: 12px;
        max-width: 700px;
        margin: 1rem auto;
        padding: 1rem 1.5rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease;
        }

        /* Alert Info */
        .alert-info-custom {
        background: var(--warna-primer);
        color: #fff;
        font-weight: 600;
        border: none;
        }

        /* Alert Warning */
        .alert-warning-custom {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        color: #4a2c00;
        font-weight: 600;
        border: none;
        }

        /* Text center & strong style */
        .alert-custom.text-center {
        text-align: center;
        }

        .alert-custom strong {
        font-size: 1.1rem;
        display: block;
        margin-bottom: 0.3rem;
        }

        /* Responsive */
        @media (max-width: 576px) {
        .alert-custom {
            margin: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        .alert-custom strong {
            font-size: 1rem;
        }
        }
        /* ===================== */

        /* Styling umum footer */
        footer.footer-pembayaran {
        background-color: #1e1e2f;
        color: #ddd;
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        footer.footer-pembayaran h5 {
        color: #fff;
        margin-bottom: 1rem;
        font-weight: 700;
        font-size: 1.2rem;
        }

        .container-bank {
        max-width: 1100px;
        margin: 0 auto 1.5rem auto;
        padding: 0 1rem;
        }

        .bank-list {
        display: flex;
        flex-wrap: wrap;
        }

        .bank-row {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        }

        /* Link styling */
        .bank-row a {
        color: #aaa;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
        }

        .bank-row a:hover {
        color: #00b4d8; /* warna biru menarik saat hover */
        }

        /* Gambar bank/payment icons */
        .bank-row img {
        height: 40px;
        margin-right: 1rem;
        filter: brightness(0.8);
        transition: filter 0.3s ease;
        cursor: pointer;
        }

        .bank-row img:hover {
        filter: brightness(1);
        }

        /* Garis pemisah */
        footer.footer-pembayaran hr {
        border: 0;
        border-top: 1px solid #444;
        margin: 1rem 0 1.5rem 0;
        }

        /* Footer copyright */
        footer.footer {
        background-color: #141421;
        color: #888;
        text-align: center;
        padding: 1rem 0;
        font-size: 0.9rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Responsive untuk mobile */
        @media (max-width: 600px) {
        .bank-list {
            justify-content: center;
        }

        .bank-row {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.8rem;
        }

        .bank-row a {
            font-size: 0.9rem;
        }
        
        .bank-row img {
            height: 32px;
            margin: 0 0.6rem 0.6rem 0;
        }
        }


        /* Overlay full screen semi-transparent */
        #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5); /* semi-transparent dark background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050; /* pastikan di atas elemen lain */
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        }

        /* Overlay aktif muncul */
        #overlay.active {
        opacity: 1;
        pointer-events: auto;
        }

        /* Konten overlay */
        .overlay-content {
        background-color: var(--warna-primer);
        color: white;
        padding: 1.5rem 2.5rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-width: 280px;
        max-width: 90vw;
        }

        /* Icon di overlay */
        .overlay-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        animation: bounceIn 0.5s ease forwards;
        }

        /* Animasi bounce masuk */
        @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        60% {
            transform: scale(1.1);
            opacity: 1;
        }
        100% {
            transform: scale(1);
        }
        }

        /* Teks overlay */
        .overlay-content p {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        }

        /* Responsive kecil */
        @media (max-width: 480px) {
        .overlay-content {
            padding: 1rem 1.5rem;
            font-size: 0.95rem;
        }

        .overlay-icon {
            font-size: 2.5rem;
        }
        }


        /* Background navbar */
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

<!-- Overlay Success -->
<div class="overlay" id="overlay">
  <div class="overlay-content">
    <i class="bi bi-cart-plus overlay-icon"></i>
    <p class="mb-0">Berhasil ditambahkan ke Keranjang!</p>
  </div>
</div>

<!-- Banner / Gambar Header 
<div class="text-center mt-0">
  <img src="assets/img/illustration1.png" alt="Toko Online Banner" class="img-fluid mb-3 w-100" style="max-height: 300px; object-fit: cover;">
</div>-->

<!-- Welcome Area -->
<div class="alert alert-custom alert-info-custom text-center">
  <strong>Selamat datang!</strong> Temukan berbagai produk menarik dengan harga terbaik.
</div>

<!-- Warning Area -->
<div class="alert alert-custom alert-warning-custom text-center">
  <strong>Note:</strong> Kami hanya melayani wilayah Masohi dan sekitarnya. Untuk luar daerah, mohon maaf belum tersedia.
</div>



    <?php
    include("api/slider.php");
    include("assets/navbar.php");
    include("assets/canvas_side.php");
    //include("api/popup_modal.php");
    //include("assets/menu.php");
    ?>

    <!-- Filter Produk -->
    <div id ="filterProduk" class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-left "><i class="bi bi-tags"></i> Filter</h5>
        <form method="GET" class="d-flex gap-2">
            <select id="filterKategori" name="kategori" class="form-select w-auto" onchange="this.form.submit()">
                <option value="semua" <?= (!isset($_GET['kategori']) || $_GET['kategori'] == 'semua') ? 'selected' : ''; ?>>..:: Semua ::..</option>
                <option value="makanan" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'makanan') ? 'selected' : ''; ?>>..:: Makanan ::.. </option>
                <option value="minuman" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'minuman') ? 'selected' : ''; ?>>..:: Minuman ::..</option>
            </select>

            <select id="filterHarga" name="filter" class="form-select w-auto" onchange="this.form.submit()">
                <option value="default" <?= (!isset($_GET['filter']) || $_GET['filter'] == 'default') ? 'selected' : ''; ?>>..:: Default ::..</option>
                <option value="harga_lama" <?= (isset($_GET['filter']) && $_GET['filter'] == 'harga_lama') ? 'selected' : ''; ?>>..:: Promo ::..</option>
                <option value="harga_terendah" <?= (isset($_GET['filter']) && $_GET['filter'] == 'harga_terendah') ? 'selected' : ''; ?>>..:: Murah ::..</option>
            </select>
        </form>
    </div>
</div>


    <?php
    include("assets/produk1.php");
    //include("assets/produk_promo.php");
    ?>

    <?php
    include("assets/nav_bottom.php");
    include("assets/footer.php");
    ?>

<script>
    // Coba ambil nama toko dari meta tag
    let shopName = document.querySelector('meta[name="shop-name"]')?.content;
    if (!shopName) {
        shopName = window.location.hostname; // Jika tidak ada, pakai hostname
    }
    document.getElementById("website-name").textContent = shopName;
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-toko").forEach(button => {
                button.addEventListener("click", function() {
                    this.style.transform = "scale(0.95)";
                    setTimeout(() => {
                        this.style.transform = "scale(1)";
                    }, 150);
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".jumlah-input").forEach(input => {
                input.addEventListener("input", function() {
                    let produkId = this.getAttribute("data-id");
                    document.getElementById("beli-jumlah-" + produkId).value = this.value;
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".form-beli").submit(function(e) {
                e.preventDefault(); // Mencegah reload halaman

                var form = $(this);
                var formData = form.serialize(); // Ambil data form

                $.ajax({
                    type: "POST",
                    url: "assets/beli.php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        console.log("Response dari server:", response);

                        if (response.success) {
                            alert(response.message); // Tampilkan pesan sukses
                            window.location.href = "assets/keranjang.php"; // Redirect ke keranjang
                        } else {
                            alert("Gagal membeli: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                        alert("Terjadi kesalahan: " + xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // Form tambah ke keranjang
    $(".form-keranjang").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            type: "POST",
            url: "assets/tambah_keranjang.php",
            data: formData,
            dataType: "json",
            success: function(response) {
    console.log("Response dari server:", response); // Debugging
    if (response.success) {
        Swal.fire({
            title: "Berhasil!",
            text: response.message,
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
            width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
        });
        updateCartCount();
    } else {
        Swal.fire({
            title: "Gagal!",
            text: response.message,
            icon: "error",
            width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
        });
    }


            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                Swal.fire({
                    title: "Oops!",
                    text: "Terjadi kesalahan saat menambahkan ke keranjang.",
                    icon: "error"
                });
            }
        });
    });

    // Update saldo secara dinamis
    function updateSaldo() {
        $.ajax({
            url: "api/get_saldo.php",
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $(".saldo-dinamis").text("Rp " + new Intl.NumberFormat("id-ID").format(response.saldo));
                }
            },
            error: function(xhr) {
                console.error("Gagal mengambil saldo:", xhr.responseText);
            }
        });
    }

    // Update saldo setiap 10 detik
    setInterval(updateSaldo, 10000);
    updateSaldo();

    // Update jumlah item di keranjang
    function updateCartCount() {
        $.ajax({
            url: "api/cart_count.php",
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let count = parseInt(response.count) || 0;
                    $("#cart-count").text(count).show();
                } else {
                    console.error("Gagal mengambil data keranjang:", response.error);
                }
            },
            error: function(xhr) {
                console.error("Error AJAX:", xhr.responseText);
            }
        });
    }

    updateCartCount();
    setInterval(updateCartCount, 5000);

    // Validasi jumlah produk sesuai stok
    $(".jumlah-input").on("input", function() {
        let max = parseInt($(this).attr("max"));
        let value = parseInt($(this).val());

        if (value > max) {
            Swal.fire({
                title: "Stok Tidak Cukup!",
                text: "Jumlah yang Anda masukkan melebihi stok yang tersedia.",
                icon: "warning"
            });
            $(this).val(max);
        }
    });

});
</script>

<script>
        // Fungsi untuk mendeteksi koneksi internet
        function checkConnection() {
            if (!navigator.onLine) {
                // Jika koneksi terputus, alihkan ke offline.php
                window.location.href = 'offline.php';
            }
        }

        // Cek koneksi saat halaman dimuat
        window.addEventListener('load', checkConnection);

        // Cek koneksi saat status berubah (offline)
        window.addEventListener('offline', () => {
            window.location.href = 'offline.php';
        });
    </script>

<script>
function updateCartCount() {
    fetch('api/cart_count.php')
        .then(response => response.json())
        .then(data => {
            let badge = document.getElementById('cart-count');
            if (badge) {
                if (data.success) {
                    badge.textContent = data.count; // Tampilkan jumlah, termasuk jika 0
                    badge.style.display = "inline-block"; // Jangan sembunyikan badge
                }
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
    setInterval(updateCartCount, 1000);
});
</script>



</body>
</html>