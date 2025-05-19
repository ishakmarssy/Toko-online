<?php
//session_start();
include("../assets/kon.php");
if (!isset($_GET['id'])) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='../index.php';</script>";
    exit;
}

// Ambil nomor WhatsApp dari tabel admin
$query_wa = "SELECT no_wa FROM admin LIMIT 1";
$result_wa = $conn->query($query_wa);
$admin_wa = $result_wa->fetch_assoc()["no_wa"] ?? "62XXXXXXXXXX"; // Default jika tidak ada data

$id = intval($_GET['id']);
$sql = "SELECT * FROM produk WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='../index.php';</script>";
    exit;
}

$produk = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($produk['nama']); ?> - Detail Produk</title>

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background-color:rgb(244, 244, 244);
            font-family: 'Poppins', sans-serif;
            margin-bottom: 90px;
            margin-top: 70px;
            color: #333;
        }

        .navbar {
            background: #fff; /* Latar belakang putih */
            color: #000; /* Warna teks hitam */
            padding: 10px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            
        }


        .navbar a {
            color: #000;
            font-size: 20px;
        }

        .product-container {
            padding: 20px;
            margin-top: 60px;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.05);
        }

        /* Styling gambar produk */
        .product-image {
            width: 80%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        /* Efek hover untuk gambar */
        .product-image:hover {
            transform: scale(1.05);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Box informasi produk */
        .product-info {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Judul produk */
        .product-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* Harga produk */
        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: #EE5A24;
        }

       


                /* Tombol beli */
        .btn-buy {
            width: 100%;
            background: #EE5A24;
            color: white;
            font-size: 16px;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 6px rgba(238, 90, 36, 0.3);
        }

        /* Efek hover untuk tombol beli */
        .btn-buy:hover {
            background-color: #ff6a00;
            color: #fff;
            transform: scale(1.03);
            box-shadow: 0px 6px 12px rgba(238, 90, 36, 0.4);
        }

        /* Area tombol di bagian bawah */
        .bottom-buttons {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgb(255, 255, 255);
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        /* Styling untuk order button */
        .order {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 10px;
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
        }

        /* Tombol dalam order */
        .order .btn {
            flex: 1;
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        /* Hover efek pada tombol order */
        .order .btn:hover {
            transform: scale(1.03);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        

        .btn-success {
            background-color: #25d366;
            color: white;
            border: none;
        }

        .btn-success:hover {
            background-color: #1ebc57;
        }
        .modal-content {
            background: #fff; /* Latar belakang putih */
            color: #000; /* Warna teks hitam */
            border-radius: 10px; /* Sudut melengkung */
            border: 2px solid #000; /* Border hitam solid */
            /* filter: grayscale(100%) contrast(1.2); /* Efek hitam-putih dengan kontras lebih tinggi */
            box-shadow: 3px 3px 0px #000; /* Efek sketsa bayangan */
            padding: 15px;
            text-align: center; /* Pusatkan teks dalam modal */
        }
        .modal-dialog {
            max-width: 350px; /* Sesuaikan ukuran modal */
            margin: auto; /* Pastikan modal tetap di tengah */

        }

        .modal{
            background:rgba(72, 72, 72, 0);
            border-color:rgb(188, 42, 42);
        }
        .modal-title {
            font-weight: bold;
            font-size: 13px;
        }
        .form-label{
            font-size: 14px;
            font-weight: 500;
        }
        .form-control{
            border-radius: 6px;
            padding: 8px;
            font-size: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .modal-backdrop {
            background-color: rgba(85, 85, 85, 0.12) !important; /* Warna putih transparan */
        }
        .x-modal{
            background-color: #EE5A24;
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

        .product-card {
            position: relative;
            background: white;
            padding: 15px;
            border: 2px solid black;
            border-radius: 10px;
            box-shadow: 3px 3px 0px black;
            transition: all 0.3s ease-in-out;
        }



        /* Efek Coretan di Samping */
        .product-card::before, .product-card::after {
            content: "";
            position: absolute;
            width: 50px;
            height: 5px;
            background: black;
            opacity: 0.3;
        }

        .product-card::before {
            top: -5px;
            left: 10px;
            transform: rotate(-10deg);
        }

        .product-card::after {
            bottom: -5px;
            right: 10px;
            transform: rotate(10deg);
        }

        /* Efek Sketsa untuk Gambar Produk */
        .product-card img {
            width: 100%;
            border: 2px dashed black;
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
        }

        .product-card img:hover {
            transform: scale(1.05) rotate(-2deg);
        }
    </style>
    <style>
         /* Responsif untuk layar kecil */
         @media (max-width: 768px) {
            :root {
                --warna-primer: #0D4715;
                --warna-sekunder: #c23616;
                --warna-teks: #333;
                --warna-habis: #ff3838;
            }

            .product-container {
                padding: 10px;
                margin-top: 50px;
            }
            .product-image {
                width: 100%;
                max-height: 300px;
            }
            .product-info {
                padding: 15px;
            }
            .product-title {
                font-size: 16px;
            }
            .product-price {
                font-size: 18px;
            }
            .btn-buy {
                font-size: 14px;
                padding: 10px;
            }
            .bottom-buttons {
                height: 60px;
                padding: 10px;
            }
            .order .btn {
                font-size: 14px;
                padding: 10px;
            }
            body {
                background-color:rgb(244, 244, 244);
                font-family: 'Poppins', sans-serif;
                margin-bottom: 90px;
                margin-top: 70px;
                color: #333;
            }
            .navbar {
                background: var(--warna-primer);
                color: #fff; /* Warna teks hitam */
                padding: 10px;
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1000;
            }
            .navbar a {
                color: #fff;
                font-size: 20px;
            }
            .product-container {
                padding: 20px;
                margin-top: 60px;
                text-align: center;
                background: white;
                border-radius: 10px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.05);
            }
            /* Styling gambar produk */
            .product-image {
                width: 80%;
                max-height: 400px;
                object-fit: cover;
                border-radius: 10px;
                transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            }
            /* Efek hover untuk gambar */
            .product-image:hover {
                transform: scale(1.05);
                box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
            }
            /* Box informasi produk */
            .product-info {
                background: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }
            /* Judul produk */
            .product-title {
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 5px;
            }
            /* Harga produk */
            .product-price {
                font-size: 20px;
                font-weight: 700;
                color: #EE5A24;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar d-flex justify-content-between align-items-center px-3">
        <div>
            <a href="javascript:history.back()" class="me-3"><i class="bi bi-arrow-left "></i></a>
        </div>
        <div>
            <a href="../assets/keranjang.php" class="me-3"><i class="bi bi-cart3 "></i></a>
            <a href="#" onclick="sharePage(event)"><i class="bi bi-share-fill "></i></a>
        </div>
    </nav>

    <!-- Gambar Produk -->
    <div class="container product-card mt-5">
        <img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" class="product-image" alt="<?= htmlspecialchars($produk['nama']); ?>">
    </div>

    <!-- Detail Produk -->
    <div class="card product-card mt-3 m-3">
        <div class="product-info">
            <h5 class="product-title"> <?= htmlspecialchars($produk['nama']); ?> </h5>
            <!--
            <?php if ($produk["harga_lama"] > 0): ?>
                <p class="text-muted"><s>Rp.<?= number_format($produk["harga_lama"], 0, ',', '.'); ?></s></p>
            <?php endif; ?>
            -->
            <p class="product-price">Rp. <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
            <hr>
            <h4 class="product-title">Deskripsi</h4>
            <p><?= nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
            <hr>
            
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="purchaseModalLabel">Konfirmasi Pembelian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="purchaseForm" action="../assets/beli.php" method="POST">
                    <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">

                    <!-- Info stok tersedia -->
                    <p class="text-muted mb-0">Stok tersedia: <strong><?= $produk['stok']; ?></strong></p>

                    <label for="jumlah" class="form-label"></label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control mb-2" value="1" min="1" max="<?= $produk['stok']; ?>" required>
                    
                    
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-buy" form="purchaseForm">Beli Sekarang</button>
            </div>
        </div>
    </div>
</div>


    <!-- Modal Keranjang -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Tambahkan ke Keranjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cartForm" action="../assets/tambah_keranjang.php" method="POST">
                        <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">
                        <label for="jumlahKeranjang" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlahKeranjang" class="form-control" value="1" min="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" form="cartForm">Tambah ke Keranjang</button>
                </div>
            </div>
        </div>
    </div>

    

    <?php if (isset($_SESSION["user_id"])): ?>
    <!-- Tombol Pembelian -->
    <div class="order text-center py-1">
        <!--<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="bi bi-cart-plus"></i> Keranjang</button>-->

        <!-- Tombol Beli -->
        <button class="btn btn-buy" data-bs-toggle="modal" data-bs-target="#purchaseModal">
            <i class="bi bi-bag-check"></i> Beli
        </button>

        <!-- Tombol WhatsApp -->
        <a href="https://wa.me/<?= $admin_wa; ?>?text=Halo%20saya%20ingin%20membeli%20produk%20ini" target="_blank" class="btn btn-success">
            <i class="bi bi-whatsapp"></i> Beli via WA
        </a>
    </div>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".form-keranjang").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    fetch("../assets/tambah_keranjang.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                            } else {
                                alert("Gagal menambahkan ke keranjang: " + data.message);
                            }
                        })
                        .catch(error => {
                            alert("Terjadi kesalahan: " + error);
                        });
                });
            });

            document.querySelectorAll(".jumlah-input").forEach(input => {
                input.addEventListener("input", function() {
                    let produkId = this.getAttribute("data-id");
                    document.getElementById("beli-jumlah-" + produkId).value = this.value;
                });
            });
        });
    </script>

<script>
function sharePage(event) {
    event.preventDefault();
    
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: "Lihat produk keren ini!",
            url: window.location.href
        }).then(() => {
            Swal.fire({
                icon: "success",
                title: "Tautan Dibagikan!",
                text: "Produk berhasil dibagikan.",
                width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
            });
        }).catch((error) => {
            console.error('Gagal membagikan:', error);
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                icon: "success",
                title: "Tautan Disalin!",
                text: "Link produk berhasil disalin ke clipboard.",
                width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
            });
        }).catch((error) => {
            console.error('Gagal menyalin tautan:', error);
        });
    }
}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        //fungsi cek stok agar tidak melebihi pembelian
        document.addEventListener("DOMContentLoaded", function () {
    const jumlahInput = document.getElementById("jumlah");
    const purchaseForm = document.getElementById("purchaseForm");
    const submitButton = document.querySelector(".btn-buy");

    // Ambil stok dari atribut data-stok (dapat disesuaikan sesuai kebutuhan)
    let stokTersedia = <?= $produk['stok']; ?>;

    purchaseForm.addEventListener("submit", function (event) {
        let jumlahDibeli = parseInt(jumlahInput.value, 10);

        if (jumlahDibeli > stokTersedia) {
            event.preventDefault();
            Swal.fire({
                icon: "warning",
                title: "Stok Tidak Cukup!",
                text: "Maksimum pembelian " + stokTersedia + " Pcs",
                width: "300px",
                customClass: {
                    popup: "swal-kecil"
                }
            });
        }
    });
});
    </script>

</body>

</html>