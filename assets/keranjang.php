<?php
//session_start();
include("kon.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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

$user_id = $_SESSION['user_id'];

$sql = "SELECT k.id AS keranjang_id, p.nama, p.harga, p.gambar, p.stok, k.jumlah 
        FROM keranjang k
        JOIN produk p ON k.produk_id = p.id
        WHERE k.user_id = ?
        ORDER BY k.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_harga = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        :root {
                --warna-primer: #ee4d2d;
                --warna-sekunder: #c23616;
                --warna-teks: #333;
                --warna-habis: #ff3838;
                --warna-link: #f1c40f;
            }
            body {
                background-color:rgb(255, 255, 255);
                padding-bottom: 50px;
                padding-top: 50px;
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
        color: var(--warna-primer);
        }

        .navbar-custom .navbar-brand:hover {
        color: var(--warna-primer);
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

        /* Overlay Notification */
        .overlay {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: rgba(25, 135, 84, 0.95); /* hijau transparan */
        color: #fff;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 1050;
        display: none;
        animation: fadeSlideIn 0.5s ease-out forwards;
        }

        .overlay-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
        font-weight: 500;
        }

        .overlay-icon {
        font-size: 1.5rem;
        }

        /* Animasi muncul */
        @keyframes fadeSlideIn {
        0% {
            opacity: 0;
            transform: translateY(-15px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
        }

        .cart-container {
        max-width: 800px;
        margin: auto;
        padding-bottom: 100px;
        }

        .cart-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        background-color: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
        position: relative;
        }

        .cart-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        }

        .cart-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        }

        .cart-info h6 {
        margin-bottom: 5px;
        font-size: 16px;
        font-weight: 600;
        }

        .harga-produk {
        color: #ff5722;
        font-weight: bold;
        margin-bottom: 5px;
        }

        .subtotal-produk {
        color: #555;
        font-weight: 500;
        }

        .input-group p {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
        }

        .btn-warna {
        background-color: #f0f0f0;
        border: none;
        padding: 5px 10px;
        font-weight: bold;
        border-radius: 4px;
        }

        .jumlah-produk {
        min-width: 24px;
        text-align: center;
        display: inline-block;
        }

        .btn-hapus {
        position: absolute;
        top: 8px;
        right: 8px;
        color: #888;
        background: none;
        border: none;
        }

        .btn-hapus:hover {
        color: red;
        }

        .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 20px;
        }

        .btn-bayar {
        background-color: #ff5722;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.3s;
        }

        .btn-bayar:hover {
        background-color: #e64a19;
        }

        .text-warna {
        color: #757575;
        }

        .text-warna2 {
        color: #e53935;
        font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 576px) {
        .cart-item {
            flex-direction: column;
            align-items: center;
        }

        .cart-img {
            width: 100%;
            height: auto;
        }

        .input-group p {
            flex-wrap: wrap;
            justify-content: center;
        }

        .cart-footer {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }

        .btn-bayar {
            width: 100%;
        }
        }
        @media (max-width: 576px) {
        .cart-container {
            padding: 10px;
        }

        .cart-item {
            flex-direction: row;
            align-items: flex-start;
            padding: 10px;
            gap: 10px;
        }

        .cart-img {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            object-fit: cover;
        }

        .cart-info {
            flex: 1;
            font-size: 14px;
        }

        .cart-info h6 {
            font-size: 14px;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .harga-produk {
            font-size: 13px;
            margin-bottom: 3px;
        }

        .subtotal-produk {
            font-size: 13px;
        }

        .input-group p {
            font-size: 13px;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: flex-start;
        }

        .btn-warna {
            padding: 3px 8px;
            font-size: 13px;
        }

        .jumlah-produk {
            font-size: 13px;
        }

        .btn-hapus {
            top: 5px;
            right: 5px;
            font-size: 14px;
        }

        .cart-footer {
            flex-direction: column;
            gap: 10px;
            text-align: center;
            padding: 12px;
        }

        .cart-footer h5 {
            font-size: 16px;
        }

        .btn-bayar {
            width: 100%;
            font-size: 14px;
            padding: 10px;
        }
        }

        .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        z-index: 1000;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.05);
        }

        .sticky-footer .btn-bayar {
        background-color: #ee4d2d;
        color: #fff;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
        }

        .sticky-footer .btn-bayar:hover {
        background-color: #d7442a;
        }

        @media (max-width: 576px) {
        .sticky-footer {
            padding: 10px;
        }

        .sticky-footer h5 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .sticky-footer .btn-bayar {
            width: auto;
            font-size: 14px;
        }
        }

        /* Tambahan: agar isi konten tidak tertutup tombol */
        .cart-container {
        padding-bottom: 90px; /* beri ruang bawah agar tidak ketutup tombol */
        }


    </style>



</head>

<body>

    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <p><i class="bi bi-cart-x overlay-icon"></i> Berhasil di hapus dari Keranjang!</p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-light shadow navbar-custom fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="javascript:history.back()" class="navbar-brand"><i class="bi bi-arrow-left"></i></a>
            <a class="navbar-brand"  href="../index.php"><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i></a>
            <a href="../assets/keranjang.php" class="navbar-brand"><i class="bi bi-cart3"></i></a>
        </div>
    </nav>

    <div class="container mt-4 cart-container">

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $subtotal = $row['harga'] * $row['jumlah']; ?>
                <div class="cart-item" data-id="<?= $row['keranjang_id']; ?>">
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" class="cart-img">
                    <div class="cart-info">
                        <h6><?= htmlspecialchars($row['nama']); ?></h6>
                        <p class="mb-0 harga-produk"><strong>Rp.<?= number_format($row['harga']); ?></strong></p>
                        <p class="text-warna  mb-0 mt-1">Subtotal: <strong class="subtotal-produk">Rp.<?= number_format($subtotal); ?></strong></p>
                        <div class="input-group ">
                        <p class="mb-0">
                            Jumlah :
                            <button class="btn btn-sm btn-warna text-center btn-kurang"><strong>-</strong></button>
                            <strong class="jumlah-produk m-1" data-stok="<?= $row['stok']; ?>"> <?= $row['jumlah']; ?> </strong>
                            <button class="btn btn-sm btn-warna btn-tambah" <?= ($row['jumlah'] >= $row['stok']) ? 'disabled' : ''; ?>>+</button>
                        </p>
            </div>
                    </div>
                    <button class="btn btn-sm btn-hapus btn-sm" data-id="<?= $row['keranjang_id']; ?>">
                    <!-- tombol x / hapus -->
                        <i class="bi bi-x-lg oc-icon"></i>
                    <!-- tombol x / hapus -->
                    </button>
                </div>
                <?php $total_harga += $subtotal; ?>
            <?php endwhile; ?>

            <div class="cart-footer sticky-footer">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <small class="text-muted">Total:</small>
            <h5 class="mb-0 text-warna2" id="total-harga">Rp<?= number_format($total_harga); ?></h5>
        </div>
        <a href="../api/checkout_keranjang.php" class="btn btn-bayar">
            <i class="bi bi-credit-card-fill"></i> Bayar
        </a>
    </div>
</div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-circle"></i> Keranjang belanja kosong.
            </div>
        <?php endif; ?>
    </div>

    <!-- Navigasi Bawah -->
    <?php
    if (isset($_SESSION['user_id'])) {
    ?>
        <!-- Navbar Bawah (Hanya Jika Sudah Login) -->
        <!-- <nav class="navbar bg-warna fixed-bottom shadow navbar-bottom">
            <div class="container d-flex justify-content-around">
                <a href="../index.php" class="nav-link text-center ">
                    <i class="material-icons ">home</i><span>Home</span>
                </a>
                <a href="../api/status_pesanan.php" class="nav-link text-center ">
                    <i class="material-icons">history</i><span>Riwayat</span>
                </a>
                <a href="#" class="nav-link text-center  active">
                    <i class="material-icons">shopping_cart</i><span>Keranjang</span>
                </a>
                <a href="profil.php" class="nav-link text-center ">
                    <i class="material-icons">account_circle</i><span>Akun</span>
                </a>
                <a  data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBothOptions"
                    aria-controls="offcanvasWithBothOptions"
                    href="#"
                    class="nav-link text-center ">
                    <i class="material-icons">dashboard</i><br><span>Menu</span>
                </a>
            </div>
        </nav> -->
    <?php
    }
    ?>

    <script>
        $(document).ready(function() {
            function updateTotalHarga() {
                let total = 0;
                $(".subtotal-produk").each(function() {
                    let harga = parseInt($(this).text().replace(/[^0-9]/g, "")) || 0;
                    total += harga;
                });
                $("#total-harga").text("Rp" + total.toLocaleString("id-ID"));
            }

            $(document).on("click", ".btn-tambah, .btn-kurang", function() {
                let row = $(this).closest(".cart-item");
                let id = row.data("id");
                let jumlahElem = row.find(".jumlah-produk");
                let harga = parseInt(row.find(".harga-produk").text().replace(/[^0-9]/g, ""));
                let jumlah = parseInt(jumlahElem.text());
                let stok = parseInt(jumlahElem.attr("data-stok"));
                let action = $(this).hasClass("btn-tambah") ? "tambah" : "kurang";

                if (action === "tambah" && jumlah >= stok) {
                    alert("Jumlah tidak bisa melebihi stok tersedia!");
                    return;
                }

                if (action === "kurang" && jumlah <= 1) return;


                $.post("../api/update_jumlah_keranjang.php", {
                    id_keranjang: id,
                    action: action
                }, function(response) {
                    if (response.status === "success") {
                        jumlahElem.text(response.jumlah);
                        row.find(".subtotal-produk").text("Rp" + (response.jumlah * harga).toLocaleString("id-ID"));

                        if (response.jumlah >= stok) {
                            row.find(".btn-tambah").prop("disabled", true);
                        } else {
                            row.find(".btn-tambah").prop("disabled", false);
                        }

                        updateTotalHarga();
                    } else {
                        alert(response.message);
                    }
                }, "json");
            });

            $(document).on("click", ".btn-hapus", function() {
                let row = $(this).closest(".cart-item");
                let id = row.data("id");

                if (!confirm("Apakah Anda yakin ingin menghapus produk ini dari keranjang?")) return;

                $.post("hapus_keranjang.php", {
                    id_keranjang: id
                }, function(response) {
                    if (response.status === "success") {
                        $("#overlay").fadeIn(); // Menampilkan overlay

                        // Menutup overlay secara otomatis setelah 3.5 detik
                        setTimeout(function() {
                            $("#overlay").fadeOut();
                        }, 2000);
                        row.slideUp(300, function() {
                            $(this).remove();
                            updateTotalHarga();
                        });
                    } else {
                        alert(response.message);
                    }
                }, "json");
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
$stmt->close();
$conn->close();
?>