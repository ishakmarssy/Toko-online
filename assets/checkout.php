<?php
//session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
include("kon.php");

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

if (!isset($_SESSION["beli_sekarang"])) {
    echo "<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: 'Tidak ada produk untuk dibeli!',
    confirmButtonColor: '#EE5A24'
}).then(() => {
    window.location = '../index.php';
});
</script>";

    exit();
}

$user_id = $_SESSION["user_id"];
$produk = $_SESSION["beli_sekarang"];
$produk_id = $produk["id"];
$jumlah = $produk["jumlah"];
$harga_produk = $produk["harga"];
$total_harga = $harga_produk * $jumlah;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color:rgb(255, 255, 255);
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
        /* Tombol Checkout dengan Efek Hover */
.btn-checkout {
    background-color: #EE5A24;
    border-radius: 8px;
    font-weight: bold;
    color: white;
    padding: 12px 20px;
    transition: all 0.3s ease-in-out;
    box-shadow: 3px 3px 0px rgba(0, 0, 0, 0.2);
}

.btn-checkout:hover {
    background-color: #D84315;
    transform: scale(1.05);
    box-shadow: 5px 5px 0px rgba(0, 0, 0, 0.3);
}

/* Efek Animasi untuk Text Warna */
.text-warna {
    color: #EE5A24;
    font-size: 2rem;
    font-weight: bold;
    text-shadow: 2px 2px 0px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.text-warna:hover {
    text-shadow: 4px 4px 0px rgba(0, 0, 0, 0.2);
    transform: scale(1.1);
}

/* Kartu Harga dengan Efek Bayangan */
.card-harga {
    background: #30336b;
    color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out;
}

.card-harga:hover {
    transform: translateY(-5px);
    box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.3);
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

    </style>
</head>
<body>
<h4 class="text-center text-warna">Checkout <i class="bi bi-bag-check-fill"></i></h4>
<div class="container mt-4">
    <div class="card carda">
        <div class="d-flex align-items-center mb-0">
            <img src="../uploads/<?= $produk['gambar']; ?>" class="product-img">
            <div class="ms-3">
                <h6><?= htmlspecialchars($produk['nama']); ?></h6>
                <p class=" mb-1">Rp<?= number_format($harga_produk, 0, ',', '.'); ?></p>
                <p class="mb-0">Jumlah: <?= $jumlah; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-2">
    <div class="card card-harga">
        <!--Guter Kolom-->
        <div class="container px-4 text-center ">
            <div class="row gx-5">
                <div class="col">
                <h6 class="text-start text-light">Harga: <strong>Rp<span id="subtotal"><?= number_format($total_harga, 0, ',', '.'); ?></span></strong></h6>
                </div>
                <div class="col">
                <h6 class="text-center text-light">Total: <strong>Rp<span id="total"><?= number_format($total_harga, 0, ',', '.'); ?></span></strong></h6>
                </div>
            </div>
        </div>
    </div>
</div>


<div class= "container mt-2">
    <div class="card">
        <form action="../api/proses_beli_sekarang.php" method="POST">
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" required><?= htmlspecialchars($alamat); ?></textarea>

            </div>
            <div class="mb-3">
                
                <label for="hp_id" class="form-label">Hp</label>
                <textarea class="form-control" id="hp_id" name="hp_id" required><?= htmlspecialchars($hp); ?></textarea>
            </div>
           
            <div class="mb-3">
                <label for="pengiriman_id" class="form-label">Lokasi</label>
                <select class="form-select" id="pengiriman_id" name="pengiriman_id" required>
                    <option value="" disabled selected>Pilih Lokasi</option>
                    <?php
                    $query_pengiriman = "SELECT * FROM pengiriman";
                    $result_pengiriman = $conn->query($query_pengiriman);
                    while ($row = $result_pengiriman->fetch_assoc()) {
                        echo "<option value='{$row['id']}' data-biaya='{$row['biaya']}'>
                                {$row['nama']} - Rp" . number_format($row['biaya'], 0, ',', '.') . "
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="metode_pembayaran_id" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="metode_pembayaran_id" name="metode_pembayaran_id" required>
                <option value="" disabled selected>Pilih Pembayaran</option>
                    <?php
                    $query_pembayaran = "SELECT * FROM metode_pembayaran";
                    $result_pembayaran = $conn->query($query_pembayaran);
                    while ($row = $result_pembayaran->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="hidden" name="total" id="total_harga_hidden" value="<?= $total_harga; ?>">
            <button type="submit" class="btn btn-checkout w-100"><i class="bi bi-wallet2"></i> Bayar</button>
        </form>
    </div>
</div>


<script>
document.getElementById("pengiriman_id").addEventListener("change", function() {
    var selectedOption = this.options[this.selectedIndex];
    var biayaPengiriman = parseInt(selectedOption.getAttribute("data-biaya")) || 0;
    var subtotal = <?= $total_harga; ?>;
    var total = subtotal + biayaPengiriman;

    document.getElementById("total").innerText = total.toLocaleString("id-ID");
    document.getElementById("total_harga_hidden").value = total;
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

<script>
document.querySelector("form").addEventListener("submit", function(event) {
    event.preventDefault(); // Mencegah form langsung terkirim
    let totalHarga = document.getElementById("total").innerText;
    Swal.fire({
        title: "Total Rp" + totalHarga + ",-",
        text: "Ingin melanjutkan  pembayaran?" ,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, bayar!",
        cancelButtonText: "Batal",
        confirmButtonColor: "#EE5A24",
        cancelButtonColor: "#6c757d",
        width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit(); // Kirim form jika pengguna menekan "Ya"
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
