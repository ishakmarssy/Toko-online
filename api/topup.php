<?php
// session_start();
include("../assets/kon.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil saldo user dari database
$user_id = $_SESSION['user_id']; // Pastikan ini ada
$sql_saldo = "SELECT saldo FROM users WHERE id = ?";
$stmt_saldo = $conn->prepare($sql_saldo);
$stmt_saldo->bind_param("i", $user_id);
$stmt_saldo->execute();
$result_saldo = $stmt_saldo->get_result();
$row_saldo = $result_saldo->fetch_assoc();

if ($row_saldo) {
    $saldo = $row_saldo['saldo'];
} else {
    $saldo = 0;
    echo "❌ User tidak ditemukan atau saldo tidak tersedia!"; // Debug
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Top-Up Saldo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 450px;
            margin-top: 50px;
        }
        .saldo-box {
            background: linear-gradient(135deg, #007bff, #00d9ff);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .topup-card {
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-submit {
            transition: all 0.3s ease-in-out;
        }
        .btn-submit:hover {
            transform: scale(1.05);
        }
        .notifikasi {
            display: none;
        }
        .icon-input {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .input-group > input, .input-group > select {
            padding-left: 40px;
        }
    </style>
    <style>
    .topup-card {
        max-width: 350px;
        margin: auto;
        border-radius: 8px;
        background: #fff;
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .input-group-text {
        background: #f8f9fa;
        border-right: 0;
    }
    .form-control, .form-select {
        border-left: 0;
        border-radius: 5px;
    }
    .btn-submit {
        transition: all 0.3s ease-in-out;
    }
    .btn-submit:hover {
        transform: scale(1.05);
    }
</style>
</head>
<body>

<div class="container">
    <!-- Kotak Saldo User -->
    <div class="saldo-box">
        <p>Saldo Anda</p>
        <h2>Rp <?= number_format($saldo, 0, ',', '.') ?></h2>
    </div>

    <!-- Card Form Top-Up -->
<div class="card shadow-sm p-4 topup-card">
    <h3 class="text-center text-primary"><i class="fas fa-wallet"></i> Top-Up Saldo</h3>
    <p class="text-center text-muted small">Pilih metode pembayaran & masukkan jumlah top-up.</p>

    <form id="form-topup" action="proses_topup.php" method="POST">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">

        <!-- Input Jumlah -->
        <div class="mb-3 position-relative">
            <label for="jumlah" class="form-label small">Jumlah Top-Up</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                <input type="number" class="form-control" name="jumlah" id="jumlah" min="10000" step="1000" 
                    placeholder="Min. 10.000" required>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-3 position-relative">
            <label for="metode" class="form-label small">Metode Pembayaran</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                <select class="form-select" name="metode" id="metode" required>
    <option value="" disabled selected>Pilih Metode Pembayaran</option>
    <?php
    // Ambil daftar metode pembayaran dari database
    $query = "SELECT nama, nomor_rekening FROM metode_pembayaran";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nama_metode = htmlspecialchars($row['nama']);
            $nomor_rekening = htmlspecialchars($row['nomor_rekening']);
            echo "<option value='$nama_metode' data-rekening='$nomor_rekening'>
                    $nama_metode - $nomor_rekening
                  </option>";
        }
    } else {
        echo "<option disabled>Tidak ada metode pembayaran tersedia</option>";
    }
    ?>
</select>

            </div>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" name="topup" class="btn btn-primary w-100 btn-sm btn-submit">
            <i class="fas fa-paper-plane"></i> Top-Up Sekarang
        </button>
    </form>

    <!-- Notifikasi -->
    <div id="notifikasi" class="alert mt-3 notifikasi"></div>
</div>



<script>
$(document).ready(function () {
    $("#form-topup").submit(function (e) {
        e.preventDefault();

        let jumlah = $("#jumlah").val();
        let metode = $("#metode").val();

        if (jumlah < 10000) {
            alert("Minimal top-up adalah Rp 10.000!");
            return;
        }

        if (confirm(Apakah Anda yakin ingin top-up sebesar Rp ${new Intl.NumberFormat('id-ID').format(jumlah)} dengan metode ${metode}?)) {
            $.ajax({
                type: "POST",
                url: "proses_topup.php",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        window.location.href = menunggu_transaksi.php?id=${response.topup_id};
                    } else {
                        alert("Gagal: " + response.message);
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("Terjadi kesalahan server.");
                }
            });
        }
    });
});
</script>


<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
