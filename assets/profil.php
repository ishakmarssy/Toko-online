<?php
include("kon.php");

// Cek apakah user sudah login
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


$sql = "SELECT nama, email, hp, username, role, foto_profil, alamat FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Cek apakah user memiliki foto profil
$foto_profil = !empty($user['foto_profil']) ? "../uploads/userImg/" . $user['foto_profil'] : "assets/default_profile.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"">
    <title>Profil Saya</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
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
            background-color: #F8F9FA;
            font-family: Arial, sans-serif;
            padding-top: 50px;
            font-family: 'Roboto', sans-serif;
        }
        /* Profile Header */
.profile-header {
  background: linear-gradient(135deg, #0d6efd, #6610f2);
  color: white;
  padding: 2rem 1rem 1.5rem;
  border-radius: 0 0 1.5rem 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 1rem;
}

.profile-img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #fff;
  margin-bottom: 1rem;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.profile-img:hover {
  transform: scale(1.05);
}

.profile-name {
  margin-bottom: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.profile-username {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.85);
}

/* Profile Body */
.profile-body {
  padding: 1.5rem;
  background-color: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}

/* Info Rows */
.user-info {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f1f1;
  font-size: 0.95rem;
  color: #495057;
}

.user-info:last-child {
  border-bottom: none;
}

.user-info i {
  font-size: 1.2rem;
  color: var(--warna-primer);
  margin-right: 0.75rem;
}

/* Responsive */
@media (max-width: 576px) {
  .profile-header {
    padding: 1.5rem 1rem;
  }

  .profile-img {
    width: 80px;
    height: 80px;
  }

  .profile-body {
    padding: 1rem;
  }
}
/* Modal Foto Profil */
#photoModal .modal-content {
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
}

#photoModal .modal-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 1.25rem;
}

#photoModal .modal-title {
  font-weight: 600;
  color: #343a40;
}

#photoModal .btn-close {
  background-color: #fff;
  border-radius: 50%;
  padding: 0.5rem;
}

#photoModal .modal-body {
  padding: 1rem;
  background-color: #ffffff;
}

#modalPhoto {
  max-width: 100%;
  height: auto;
  border-radius: 0.75rem;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

#modalPhoto:hover {
  transform: scale(1.02);
}

/* Modal Edit Profil */
#editModal .modal-content {
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

#editModal .modal-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  justify-content: end;
  padding: 1rem 1.25rem;
}

#editModal .btn-close {
  background-color: #fff;
  border-radius: 50%;
  padding: 0.5rem;
}

#editModal .modal-body {
  padding: 1.5rem;
}

#editModal .form-label {
  font-weight: 600;
  color: #495057;
}

#editModal .form-control {
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #ced4da;
  transition: border-color 0.3s;
}

#editModal .form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

#modalProfilePreview {
  object-fit: cover;
  border: 3px solid #dee2e6;
  transition: transform 0.3s ease;
}

#modalProfilePreview:hover {
  transform: scale(1.05);
}

#editModal .btn-green {
  background-color: #198754;
  color: white;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: background-color 0.3s ease;
}

#editModal .btn-green:hover {
  background-color: #157347;
}

/* Modal Ganti Password */
#passwordModal .modal-content {
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
}

#passwordModal .modal-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 1.25rem;
}

#passwordModal .modal-title {
  font-weight: 600;
  color: #343a40;
}

#passwordModal .btn-close {
  background-color: #fff;
  padding: 0.5rem;
  border-radius: 50%;
}

#passwordModal .modal-body {
  padding: 1.5rem;
}

#passwordModal .form-label {
  font-weight: 600;
  color: #495057;
}

#passwordModal .form-control {
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #ced4da;
  transition: border-color 0.3s;
}

#passwordModal .form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

#passwordModal .btn-success {
  background-color: #198754;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: background-color 0.3s ease;
}

#passwordModal .btn-success:hover {
  background-color: #157347;
}

/* Background navbar */
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
    <nav class="navbar navbar-custom shadow fixed-top">
        <div class="container d-flex align-items-center">
            <!-- Tombol Kembali -->
            <a href="javascript:history.back()" class="navbar-brand me-auto">
                <i class="bi bi-arrow-left"></i>
            </a>

            <!-- Nama Toko di Tengah -->
            <a class="navbar-brand mx-auto text-center" href="../index.php">
                <?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i>
            </a>

            <!-- Tombol Keranjang & Edit Modal di Sebelah Kanan -->
            <div class="d-flex ms-auto">
                <!--<a href="../assets/keranjang.php" class="navbar-brand me-4">
                    <i class="bi bi-cart3"></i>
                </a>-->
                <a class="navbar-brand" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="bi bi-gear"></i>
                </a>
            </div>
        </div>
    </nav>





        <div class="profile-header text-center">
            <img src="<?php echo $foto_profil; ?>" class="profile-img" id="profilePreview" data-bs-toggle="modal" data-bs-target="#photoModal">
            <h5 class="profile-name"><?php echo htmlspecialchars($user['nama']); ?></h5>
            <p class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></p>
        </div>
        <div class="profile-body">
            <div class="user-info">
                <i class="bi bi-envelope-fill"></i>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="user-info">
                <i class="bi bi-telephone-fill"></i>
                <span><?php echo htmlspecialchars($user['hp']); ?></span>
            </div>
            <div class="user-info">
                <i class="bi bi-geo-alt-fill"></i>
                <span><?php echo htmlspecialchars($user['alamat']); ?></span>
            </div>
        </div>

        <!--<div class="profile-actions">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="bi bi-pencil-square"></i> Edit Profil
            </button>-->

            <button class="btn  w-50 mt-2 m-2 " data-bs-toggle="modal" data-bs-target="#passwordModal">
                <i class="bi bi-key-fill"></i> Ganti Password
            </button>
        </div>



<!-- Modal Perbesar Foto -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?php echo $foto_profil; ?>" class="img-fluid rounded" id="modalPhoto">
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <img src="<?php echo $foto_profil; ?>" class="rounded-circle" width="100" height="100" id="modalProfilePreview">
                        <input type="file" class="form-control mt-2" name="foto" id="fotoInput" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="hp" id="hp" value="<?php echo htmlspecialchars($user['hp']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" class="form-control" name="alamat" id="nama" value="<?php echo htmlspecialchars($user['alamat']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-green w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Preview foto sebelum upload
    $("#fotoInput").change(function() {
        let reader = new FileReader();
        reader.onload = function(e) {
            $("#modalProfilePreview").attr("src", e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });

    // AJAX Submit Form
    $("#editForm").submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: "update_profil.php",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });

    // AJAX Submit Ganti Password
    $("#passwordForm").submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "update_password.php",
            data: formData,
            success: function(response) {
                alert(response);
                $("#passwordModal").modal("hide");
                $("#passwordForm")[0].reset();
            }
        });
    });

    // Perbesar Foto Profil
    document.getElementById("profilePreview").addEventListener("click", function() {
        document.getElementById("modalPhoto").src = this.src;
    });
</script>



<?php
if (isset($_SESSION['user_id'])) {
?>
<!-- Navbar Bawah (Hanya Jika Sudah Login) -->
<nav class="navbar bg-warna fixed-bottom shadow navbar-bottom">
            <div class="container d-flex justify-content-around">
                <a href="../index.php" class="nav-link text-center ">
                    <i class="material-icons ">home</i><span>Home</span>
                </a>
                <a href="../api/status_pesanan.php" class="nav-link text-center ">
                    <i class="material-icons">history</i><span>Riwayat</span>
                </a>
                <a href="keranjang.php" class="nav-link text-center ">
                    <i class="material-icons">shopping_cart</i><span>Keranjang</span>
                </a>
                <a href="#" class="nav-link text-center  active">
                    <i class="material-icons">account_circle</i><span>Akun</span>
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
    <?php
    }
    ?>

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
