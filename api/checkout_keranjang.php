<?php
//session_start();
include("../assets/kon.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT hp FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$hp = $user['hp'] ?? ''; // Jika tidak ada alamat, gunakan string kosong

$sql = "SELECT alamat FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$alamat = $user['alamat'] ?? ''; // Jika tidak ada alamat, gunakan string kosong

// Ambil produk dari keranjang
$query_keranjang = "SELECT k.produk_id, p.nama, p.harga, k.jumlah
                    FROM keranjang k
                    JOIN produk p ON k.produk_id = p.id
                    WHERE k.user_id = ?";
$stmt_keranjang = $conn->prepare($query_keranjang);
$stmt_keranjang->bind_param("i", $user_id);
$stmt_keranjang->execute();
$result_keranjang = $stmt_keranjang->get_result();

$total_harga = 0;
$produk_keranjang = [];
while ($row = $result_keranjang->fetch_assoc()) {
    $row['subtotal'] = $row['harga'] * $row['jumlah'];
    $total_harga += $row['subtotal'];
    $produk_keranjang[] = $row;
}
$stmt_keranjang->close();

if (empty($produk_keranjang)) {
    echo "<script>alert('Keranjang Anda kosong!'); window.history.back();</script>";
    exit();
}

// Ambil daftar pengiriman
$query_pengiriman = "SELECT id, nama, biaya FROM pengiriman";
$result_pengiriman = $conn->query($query_pengiriman);
$pengiriman_options = [];
while ($row = $result_pengiriman->fetch_assoc()) {
    $pengiriman_options[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function updateTotal() {
            let biayaPengiriman = parseInt(document.getElementById("pengiriman_id").selectedOptions[0].getAttribute("data-biaya")) || 0;
            let totalProduk = <?= $total_harga ?>;
            let totalKeseluruhan = totalProduk + biayaPengiriman;
            document.getElementById("total_harga_display").innerText = "Rp" + totalKeseluruhan.toLocaleString("id-ID");
            document.getElementById("total_harga").value = totalKeseluruhan;
        }
    </script>
    <style>
        body {
            background-color: rgb(255, 255, 255);
            margin-top: 15px;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }

        .card {
    position: relative;
    background: white;
    padding: 20px;
    border: 2px solid black;
    border-radius: 12px;
    box-shadow: 3px 3px 0px black, 5px 5px 0px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
}

/* Efek Hover seperti digambar ulang */
.card:hover {
    /* transform: translate(-3px, -3px);*/
    /* box-shadow: 6px 6px 0px black, 10px 10px 0px rgba(0, 0, 0, 0.2);*/
}

/* Garis-garis coretan di sudut kartu */
.card::before, .card::after {
    content: "";
    position: absolute;
    width: 40px;
    height: 4px;
    background: black;
    opacity: 0.3;
}

.card::before {
    top: -5px;
    left: 15px;
    transform: rotate(-10deg);
}

.card::after {
    bottom: -5px;
    right: 15px;
    transform: rotate(10deg);
}

/* Efek Sketsa untuk Gambar Produk */
.product-img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px dashed black;
    transition: transform 0.3s ease-in-out;
}

.product-img:hover {
    transform: scale(1.05) rotate(-2deg);
}


.btn-checkout {
    position: relative;
    background: white;
    color: #000;
    font-weight: bold;
    font-size: 16px;
    padding: 10px 20px;
    border: 2px solid #000;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    box-shadow: 3px 3px 0px rgb(0, 0, 0), 5px 5px 0px rgba(0, 0, 0, 0.2);
}

/* Efek hover seperti gambar tangan */
.btn-checkout:hover {
    transform: translate(-3px, -3px);
    box-shadow: 6px 6px 0px rgb(0, 0, 0), 10px 10px 0px rgba(0, 0, 0, 0.2);
    background:rgb(0, 0, 0);
    color: white;
}

/* Efek garis coretan di sudut */
.btn-checkout::before, 
.btn-checkout::after {
    content: "";
    position: absolute;
    width: 30px;
    height: 3px;
    background: #30336b;
    opacity: 0.3;
}

.btn-checkout::before {
    top: -5px;
    left: 15px;
    transform: rotate(-10deg);
}

.btn-checkout::after {
    bottom: -5px;
    right: 15px;
    transform: rotate(10deg);
}


        .text-warna {
            color:#000000;
            font-size: 2.0rem;
        }
        .table-warna{
            background: #000;
        }

        .text-harga {
            color: #000;
            font-size: 1.1rem;
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

.form-control {
    position: relative;
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #000;
    border-radius: 8px;
    background: white;
    color: #000;
    outline: none;
    box-shadow: 3px 3px 0px #EE5A24, 5px 5px 0px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
}

/* Efek saat input difokuskan */
.form-control:focus {
    transform: translate(-3px, -3px);
    box-shadow: 6px 6px 0px #009432, 10px 10px 0px rgba(0, 0, 0, 0.2);
    background: #f8f9fa;
}

/* Efek coretan di sudut */
.form-control::before, 
.form-control::after {
    content: "";
    position: absolute;
    width: 30px;
    height: 3px;
    background: #F79F1F;
    opacity: 0.3;
}

.form-control::before {
    top: -5px;
    left: 10px;
    transform: rotate(-10deg);
}

.form-control::after {
    bottom: -5px;
    right: 10px;
    transform: rotate(10deg);
}

/* Placeholder lebih soft */
.form-control::placeholder {
    color: rgba(48, 51, 107, 0.5);
    font-style: italic;
}

.form-select {
    position: relative;
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #30336b;
    border-radius: 8px;
    background: white;
    color: #30336b;
    outline: none;
    box-shadow: 3px 3px 0px #30336b, 5px 5px 0px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
    appearance: none; /* Menghilangkan default dropdown */
    background-image: url('data:image/svg+xml;utf8,<svg fill="%2330336b" viewBox="0 0 24 24" width="20px" height="20px" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 18px;
    cursor: pointer;
}

/* Efek saat input difokuskan */
.form-select:focus {
    transform: translate(-3px, -3px);
    box-shadow: 6px 6px 0px #30336b, 10px 10px 0px rgba(0, 0, 0, 0.2);
    background: #f8f9fa;
}

/* Efek coretan di sudut */
.form-select::before, 
.form-select::after {
    content: "";
    position: absolute;
    width: 30px;
    height: 3px;
    background: #30336b;
    opacity: 0.3;
}

.form-select::before {
    top: -5px;
    left: 10px;
    transform: rotate(-10deg);
}

.form-select::after {
    bottom: -5px;
    right: 10px;
    transform: rotate(10deg);
}

.table-bordered {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #333;
    box-shadow: 4px 4px 0px #30336b, 6px 6px 0px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
}

/* Efek garis sketsa */
.table-bordered th,
.table-bordered td {
    border: 2px solid #30336b;
    padding: 10px;
    text-align: left;
    position: relative;
}

/* Efek bayangan saat hover */
.table-bordered tbody tr:hover {
    background: #f8f9fa;
    transform: translate(-2px, -2px);
    box-shadow: 6px 6px 0px #30336b, 10px 10px 0px rgba(0, 0, 0, 0.2);
}

/* Efek coretan di sudut */
.table-bordered::before, 
.table-bordered::after {
    content: "";
    position: absolute;
    width: 40px;
    height: 3px;
    background: #30336b;
    opacity: 0.3;
}

.table-bordered::before {
    top: -5px;
    left: 10px;
    transform: rotate(-10deg);
}

.table-bordered::after {
    bottom: -5px;
    right: 10px;
    transform: rotate(10deg);
}






    </style>
</head>

<body>
    <h4 class="text-center text-warna">Checkout <i class="bi bi-cart-check-fill"></i></h4>
    <div class="container mt-4">
        <div class="card p-2">
            <table class="table table-bordered">
                <thead class="table-warna">
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produk_keranjang as $produk): ?>
                        <tr>
                            <td><?= htmlspecialchars($produk['nama']); ?></td>
                            <td>Rp.<?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td><?= $produk['jumlah']; ?> Pcs</td>
                            <td>Rp.<?= number_format($produk['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="text-end">Harga: <strong class="text-harga">Rp.<?= number_format($total_harga, 0, ',', '.'); ?></strong></p>
        </div>
        </br>
        <div class="card p-3">
            <form action="proses_pembayaran.php" method="POST">
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" required><?= htmlspecialchars($alamat); ?></textarea>

                </div>
                <div class="mb-3">
                    <label for="hp_id" class="form-label">Hp <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="hp_id" name="hp_id" required><?= htmlspecialchars($hp); ?></textarea>

                </div>
                <div class="mb-3">
                    <label for="pengiriman_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <select class="form-select" id="pengiriman_id" name="pengiriman_id" required onchange="updateTotal()">
                        <option value="" disabled selected>Pilih Lokasi</option>
                        <?php foreach ($pengiriman_options as $pengiriman): ?>
                            <option value="<?= $pengiriman['id'] ?>" data-biaya="<?= $pengiriman['biaya'] ?>">
                                ..:: <?= $pengiriman['nama'] ?> * (Rp.<?= number_format($pengiriman['biaya'], 0, ',', '.') ?>)
                            ::..</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="metode_pembayaran_id" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                    <select class="form-select" id="metode_pembayaran_id" name="metode_pembayaran_id" required>
                        <option value="" disabled selected>Pilih Pembayaran</option>
                        <?php
                        $query_pembayaran = "SELECT * FROM metode_pembayaran";
                        $result_pembayaran = $conn->query($query_pembayaran);
                        while ($row = $result_pembayaran->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>..:: {$row['nama']} ::..</option>" ;
                        }
                        ?> 
                    </select>
                </div>
                <p class="text-end">Total: <strong id="total_harga_display" class="text-harga">Rp.<?= number_format($total_harga, 0, ',', '.'); ?></strong></p>
                <input type="hidden" id="total_harga" name="total" value="<?= $total_harga; ?>">
                <button type="submit" class="btn btn-checkout w-100"><i class="bi bi-wallet2"></i> Checkout</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", updateTotal);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showTotalHarga() {
        let totalHarga = document.getElementById("total_harga_display").innerText;

        Swal.fire({
            icon: "info",
            title: "Konfirmasi Pembayaran",
            text: "Total harga " + totalHarga,
            showCancelButton: true,
            confirmButtonText: "Ya, Bayar!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector("form").submit();
            }
        });

        return false; // Mencegah form langsung submit
    }

    document.querySelector("form").addEventListener("submit", function(event) {
        event.preventDefault(); // Menghentikan submit form agar bisa dikonfirmasi
        showTotalHarga();
    });
</script>

</body>

</html>