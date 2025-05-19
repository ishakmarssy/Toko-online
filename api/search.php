<?php
//session_start();
include("../assets/kon.php");

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

// Ambil query pencarian dari URL
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Hasil Pencarian - Qinar Caffe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Menggunakan font Roboto untuk nuansa modern -->
    <style>
        :root {
                --warna-primer: #ee4d2d;
                --warna-sekunder: #c23616;
                --warna-teks: #333;
                --warna-habis: #ff3838;
                --warna-link: #f1c40f;
            }
        body {
                background-color: #fff; /* Latar belakang putih seperti kertas */
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
        color:var(--warna-primer);
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
  border-radius: 16px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  background-color: #fff;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.card img {
  height: 160px;
  object-fit: cover;
  transition: filter 0.3s ease;
  border-bottom: 1px solid #eee;
}

.card img.habis {
  filter: grayscale(100%) brightness(0.8);
}

.card-body {
  padding: 0.75rem 1rem;
}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.4rem;
}

.harga-lama {
  font-size: 0.85rem;
  margin-right: 0.5rem;
}

.card-text.text-danger {
  font-size: 1rem;
  font-weight: bold;
}

.card-text small {
  font-size: 0.8rem;
}

/* Promo badge */
.promo-overlay {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #ff5e57;
  color: white;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 8px;
  font-weight: 600;
}

/* Stok habis badge */
.stok-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #6c757d;
  color: white;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 8px;
  font-weight: 600;
}

/* Disabled product (card overlay style) */
.card.disabled-link {
  opacity: 0.6;
  pointer-events: none;
}

/* Tombol tambah keranjang */
.btn-toko {
    margin-left: 5px;
  background-color: var(--warna-primer);
  border: none;
  color: #fff;
  transition: background-color 0.3s ease;
}

.btn-toko:hover {
  background-color: var(--warna-primer);
    color: #fff;
}

/* Minus & Plus button */
.input-group button {
  padding: 0 10px;
  font-weight: bold;
}

.jumlah-text {
  padding: 0 10px;
  font-weight: 600;
  min-width: 20px;
  display: inline-block;
  text-align: center;
}

/* Grid spacing */
.row.g-3 {
  row-gap: 1.5rem;
}

/* Responsive height adjust */
@media (max-width: 576px) {
  .card img {
    height: 130px;
  }

  .card-title {
    font-size: 0.9rem;
  }

  .card-text {
    font-size: 0.85rem;
  }

  .jumlah-text {
    padding: 0 8px;
  }
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
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        background-color: #28a745; /* hijau success */
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


        /* Background navbar bawah */
        .navbar.bg-warna {
        background-color: #fff; /* biru-hijau yang kalem */
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

    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow-sm">
        <div class="container">
            <!-- Bagian kiri: Brand & Search -->
            <div class="d-flex align-items-center w-100 justify-content-between">
                <!-- <a class="navbar" href="../index.php"><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i></a> -->
                <form class="d-flex ms-1 me-2" action="search.php" method="GET">
                    <div class="input-group search-wrapper">
                        <input type="search" class="form-control" name="q" placeholder="Cari produk..." aria-label="Search">
                        <button class="btn btn-cari" type="submit">
                            <i class="bi bi-search "></i>
                        </button>
                    </div>
                </form>
                <!-- Keranjang -->
                <a href="../assets/keranjang.php" class="navbar-brand position-relative">
                    <i class="bi bi-cart3 fs-4 text-white"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-2 mb-5">
        <h3 class="mb-4">Hasil Pencarian untuk: <span class="text-danger"><?= htmlspecialchars($searchQuery) ?></span></h3>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php
            if (!empty($searchQuery)) {
                $stmt = $conn->prepare("SELECT * FROM produk WHERE nama LIKE ? ORDER BY id DESC");
                $searchTerm = "%" . $searchQuery . "%";
                $stmt->bind_param("s", $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
            ?>
                       <div class="col">

<div class="card h-100 <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>">
    <div class="position-relative">
        <!-- Detail Produk -->
        <a href="../api/detail_produk.php?id=<?= $row["id"]; ?>"
            class="text-decoration-none text-dark detail-produk <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>">

            <!-- Gambar Produk -->
            <img src="../uploads/<?= htmlspecialchars($row["gambar"]); ?>" class="card-img-top <?= ($row["stok"] <= 0) ? 'habis' : ''; ?>" alt="<?= htmlspecialchars($row["nama"]); ?>">
        </a>

        <!-- Overlay "Stok Habis" -->
        <?php if ($row["stok"] <= 0): ?>
            <div class="stok-overlay">Habis</div>
        <?php endif; ?>

        <!-- Overlay "Promo" jika harga lama tersedia -->
        <?php if ($row["harga_lama"] > 0): ?>
            <div class="promo-overlay <?= ($row['stok'] <= 0) ? 'habis' : ''; ?>">Promo</div>
        <?php endif; ?>
    </div>

    <div class="card-body d-flex flex-column">
        <!-- Nama Produk -->
        <h8 class="card-title text-black"><?= htmlspecialchars($row["nama"]); ?></h8>
        <hr>

        <!-- Harga Produk -->
        <p class="card-text text-danger mb-0"><?php if ($row["harga_lama"] > 0): ?>
                <span class="text-muted harga-lama"><s>Rp.<?= number_format($row["harga_lama"], 0, ',', '.'); ?></s>
                </span>
            <?php endif; ?>
            Rp.<?= number_format($row["harga"], 0, ',', '.'); ?>
        </p>
        <p class="card-text"> <?= substr($row['deskripsi'], 0, 20); ?>... </p>
        <!-- Stok Produk -->
        <h8 class="card-text text-black"><small class="text-muted">Stok: <?= $row['stok']; ?></small></h8>
        <?php if (isset($_SESSION["user_id"])): ?>
        <div class="d-flex align-items-center gap-1">
            <!-- Form Tambah ke Keranjang -->
            <form class="form-keranjang d-flex align-items-center" data-id="<?= $row["id"]; ?>">
                <input type="hidden" name="produk_id" value="<?= $row["id"]; ?>">
                <div class="input-group">
                    <button class="btn btn-light minus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>-</button>
                    <span class="jumlah-text" data-id="<?= $row['id']; ?>">1</span> <!-- Ganti input jadi span -->
                    <button class="btn btn-light plus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>+</button>
                </div>
                <input type="hidden" name="jumlah" class="jumlah-hidden" id="jumlah-<?= $row["id"]; ?>" value="1" max="<?= $row['stok']; ?>">
                <button  type="submit" class="btn btn-toko btn-tambah d-flex justify-content-center align-items-center"
                    style="width: 35px; height: 35px;" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus fs-3"></i>
                </button>
            </form>

            <!-- Form Beli Sekarang
            <form action="assets/beli.php" method="POST">
                <input type="hidden" name="produk_id" value="<?= $row["id"]; ?>">
                <input type="hidden" name="jumlah" class="beli-jumlah" id="beli-jumlah-<?= $row["id"]; ?>" value="1">
                <button type="submit" class="btn btn-toko btn-beli d-flex justify-content-center align-items-center"
                    style="width: 35px; height: 35px;" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                    <i class="bi bi-bag-check fs-3"></i>
                </button>
            </form> -->
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php
                    }
                } else {
                    echo '<p class="text-center">Tidak ada produk ditemukan.</p>';
                }
                $stmt->close();
            } else {
                echo '<p class="text-center">Silakan masukkan kata kunci pencarian.</p>';
            }
?>
    </div>
    </div>

    <!-- Navbar Bawah (Hanya Jika Sudah Login) -->
    <nav class="navbar bg-warna fixed-bottom shadow navbar-bottom">
            <div class="container d-flex justify-content-around">
                <a href="../index.php" class="nav-link text-center">
                    <i class="material-icons ">home</i><br><span>Home</span>
                </a>
                <a href="../api/status_pesanan.php" class="nav-link text-center">
                    <i class="material-icons">history</i><br><span>Riwayat</span>
                </a>
                <a data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions"
                aria-controls="offcanvasWithBothOptions"
                href="#"
                class="nav-link text-center">
                    <i class="material-icons">dashboard</i>
                    <span>Menu</span>
                </a>
                <a href="../assets/profil.php" class="nav-link text-center">
                    <i class="material-icons">account_circle</i><br><span>Akun</span>
                </a>
                <!--<a  data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBothOptions"
                    aria-controls="offcanvasWithBothOptions"
                    href="#"
                    class="nav-link text-center ">
                    <i class="material-icons">dashboard</i><br><span>Menu</span>
                </a>-->
            </div>
        </nav>



<footer class="footer-pembayaran">
    <div class="container-bank">
        <h5>Company</h5>
        <div class="bank-list">
            <div class="bank-row">
            <a href="../assets/doc/tentang_kami.php">Tentang Kami</a><br>
            <a href="../assets/doc/bantuan.php">Bantuan</a><br>
            <a href="../assets/doc/kebijakan_privasi.php">Kebijakan Privasi</a><br>
            <a href="../assets/doc/syarat_ketentuan.php">Syarat & Ketentuan</a><br>
            </div>
        </div>
    </div>
    <hr>
    <div class="container-bank">
        <h5>Payment & Delivery</h5>
        <div class="bank-list">
            <div class="bank-row">
                <!--<img src="uploads/bank/bca.jpg" alt="BCA">-->
                <img src="../uploads/bank/bni.png" alt="BNI">
                <img src="../uploads/bank/bri.png" alt="BRI">
                <img src="../uploads/bank/mandiri.png" alt="Mandiri">
                <img src="../uploads/bank/dana.png" alt="Dana">
                <img src="../uploads/bank/ovo.jpg" alt="OVO">
                <img src="../uploads/bank/spay.png" alt="ShoppePay">
            </div>
        </div>
    </div>
</footer>
<footer class="footer">
<a>Copyright &copy; 2025 <?php echo htmlspecialchars($store_name); ?>. All Rights Reserved.</a>
</footer>

<script>
    // Coba ambil nama toko dari meta tag
    let shopName = document.querySelector('meta[name="shop-name"]')?.content;
    if (!shopName) {
        shopName = window.location.hostname; // Jika tidak ada, pakai hostname
    }
    document.getElementById("website-name").textContent = shopName;
</script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.minus-btn').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-id');
                let jumlahSpan = document.querySelector(`.jumlah-text[data-id="${productId}"]`);
                let jumlahHidden = document.querySelector(`#jumlah-${productId}`);
                let min = 1;
                let currentValue = parseInt(jumlahSpan.innerText) || min;

                if (currentValue > min) {
                    jumlahSpan.innerText = currentValue - 1;
                    jumlahHidden.value = currentValue - 1;
                    updateBeliJumlah(productId, currentValue - 1);
                }
            });
        });

        document.querySelectorAll('.plus-btn').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-id');
                let jumlahSpan = document.querySelector(`.jumlah-text[data-id="${productId}"]`);
                let jumlahHidden = document.querySelector(`#jumlah-${productId}`);
                let max = parseInt(jumlahHidden.getAttribute('max')); // Mengambil stok dari hidden input
                let currentValue = parseInt(jumlahSpan.innerText) || 1;

                if (currentValue < max) {
                    jumlahSpan.innerText = currentValue + 1;
                    jumlahHidden.value = currentValue + 1;
                    updateBeliJumlah(productId, currentValue + 1);
                }
            });
        });

        function updateBeliJumlah(productId, value) {
            let beliJumlahInput = document.querySelector(`input.beli-jumlah[id="beli-jumlah-${productId}"]`);
            if (beliJumlahInput) {
                beliJumlahInput.value = value;
            }
        }
    });
</script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".form-keranjang").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    type: "POST",
                    url: "../assets/tambah_keranjang.php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                        } else {
                            alert("Gagal: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".jumlah-input").on("input", function() {
                var id = $(this).data("id");
                var jumlah = $(this).val();
                $("#beli-jumlah-" + id).val(jumlah);
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

</body>

</html>
<?php
$conn->close();
?>