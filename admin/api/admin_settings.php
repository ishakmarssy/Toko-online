<?php
// admin_email_settings.php
include("../db/kon.php");
//session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION["admin_id"])) {
  header("Location: ../login.php");
  exit();
}
// (Pastikan validasi session admin dilakukan agar hanya admin yang dapat mengakses halaman ini)

$error = '';
$success = '';
$error_store = '';
$success_store = '';

// Ambil data pengaturan email dari database
$sql = "SELECT * FROM email_settings WHERE id = 1";
$result = $conn->query($sql);
if($result->num_rows > 0){
    $settings = $result->fetch_assoc();
} else {
    // Jika belum ada, gunakan nilai default
    $settings = [
        'username' => '',
        'password' => '',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls'
    ];
}

// Proses update pengaturan email jika form disubmit
if (isset($_POST['submit_email'])) {
    $username   = $_POST['username'] ?? '';
    $password   = $_POST['password'] ?? '';
    $host       = $_POST['host'] ?? 'smtp.gmail.com';
    $port       = $_POST['port'] ?? 587;
    $encryption = $_POST['encryption'] ?? 'tls';
    
    $sql_update = "UPDATE email_settings SET username = ?, password = ?, host = ?, port = ?, encryption = ? WHERE id = 1";
    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param("sssis", $username, $password, $host, $port, $encryption);
        if ($stmt->execute()) {
            $success = "Pengaturan email berhasil diperbarui.";
            // Refresh pengaturan email
            $result = $conn->query("SELECT * FROM email_settings WHERE id = 1");
            if ($result && $result->num_rows > 0) {
                $settings = $result->fetch_assoc();
            }
        } else {
            $error = "Gagal memperbarui pengaturan email.";
        }
        $stmt->close();
    } else {
        $error = "Gagal mempersiapkan query untuk pengaturan email.";
    }
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




$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Pengaturan Email & Nama Toko</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
        background: #f8f9fa;
        margin-bottom: 70px;
    }
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .card-header {
        background: #30336b;
        color: #fff;
        padding: 1rem 1.0rem;
    }
    .card-header h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
    }
    .card-body {
        padding: 1.5rem;
    }
    @media (max-width: 576px) {
      .card {
         margin: 0 1rem;
      }
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <!-- Card Pengaturan Email -->
    <div class="col-md-8 col-lg-6 mb-3">
      <div class="card">
        <div class="card-header">
          <h4>Pengaturan Email</h4>
        </div>
        <div class="card-body">
          <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>
          <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
          <?php endif; ?>
          <form method="POST" action="">
              <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="text" name="username" class="form-control" placeholder="email@gmail.com" value="<?php echo htmlspecialchars($settings['username']); ?>">
              </div>
              <div class="mb-3">
                  <label class="form-label">Password App</label>
                  <input type="text" name="password" class="form-control" placeholder="xxxx xxxx xxxx xxxx" value="<?php echo htmlspecialchars($settings['password']); ?>">
              </div>
              <!-- Grid -->
                <div class="container text-center">
                  <div class="row">
                    <div class="col">
                    <div class="mb-3">
                                  <label class="form-label">SMTP Host</label>
                                  <input type="text" name="host" class="form-control" placeholder="smtp.gmail.com" value="<?php echo htmlspecialchars($settings['host']); ?>">
                              </div>
                    </div>
                    <div class="col">
                    <div class="mb-3">
                                  <label class="form-label">SMTP Port</label>
                                  <input type="number" name="port" class="form-control" placeholder="587" value="<?php echo htmlspecialchars($settings['port']); ?>">
                              </div>
                    </div>
                  </div>
                </div>
                <!-- Grid -->
              <div class="mb-3">
                  <label class="form-label">Enkripsi</label>
                  <select name="encryption" class="form-select">
                      <option value="tls" <?php if($settings['encryption'] == 'tls') echo 'selected'; ?>>TLS</option>
                      <option value="ssl" <?php if($settings['encryption'] == 'ssl') echo 'selected'; ?>>SSL</option>
                      <option value="" <?php if(empty($settings['encryption'])) echo 'selected'; ?>>None</option>
                  </select>
              </div>
              <button type="submit" name="submit_email" class="btn btn-success w-auto">Simpan Pengaturan Email</button>
          </form>
        </div>
      </div>
    </div>
    <!-- Card Pengaturan Nama Toko 
    <div class="col-md-8 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4>Pengaturan Nama Toko</h4>
        </div>
        <div class="card-body">
          <?php if($error_store): ?>
            <div class="alert alert-danger"><?php echo $error_store; ?></div>
          <?php endif; ?>
          <?php if($success_store): ?>
            <div class="alert alert-success"><?php echo $success_store; ?></div>
          <?php endif; ?>
          <form method="POST" action="">
              <div class="mb-3">
                  <label class="form-label">Nama Toko</label>
                  <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($store_settings['store_name']); ?>">
              </div>
              <button type="submit" name="submit_store" class="btn btn-success w-auto">Simpan Nama Toko</button>
          </form>
        </div>
      </div>
    </div>-->
    
    <!-- Card Pengaturan Nomor WhatsApp 
    <div class="col-md-8 col-lg-6 mt-3">
        <div class="card">
            <div class="card-header">
                <h4>Pengaturan Nomor WhatsApp</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error_wa)): ?>
                    <div class="alert alert-danger"><?php echo $error_wa; ?></div>
                <?php endif; ?>
                <?php if (isset($success_wa)): ?>
                    <div class="alert alert-success"><?php echo $success_wa; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control" value="<?php echo htmlspecialchars($admin_data['no_wa']); ?>" required>
                        <small class="text-muted">Masukkan nomor dengan format yang benar, contoh: 628123456789</small>
                    </div>
                    <button type="submit" name="submit_wa" class="btn btn-success w-auto">Simpan Nomor WhatsApp</button>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>-->
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
