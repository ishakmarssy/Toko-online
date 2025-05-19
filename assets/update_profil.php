<?php
include("kon.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Akses ditolak!";
    exit();
}

$user_id = $_SESSION['user_id'];
$nama = trim($_POST['nama']);
$email = trim($_POST['email']);
$hp = trim($_POST['hp']);
$alamat = trim($_POST['alamat']);
$username = trim($_POST['username']);
$foto_nama = "";

// Cek apakah ada file foto yang diunggah
if (!empty($_FILES['foto']['name'])) {
    $foto_nama = time() . "_" . basename($_FILES["foto"]["name"]);
    $target_dir = "../uploads/userImg/";
    $target_file = $target_dir . $foto_nama;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi format file
    $allowed_types = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowed_types)) {
        echo "Format file tidak didukung! Hanya JPG, JPEG, PNG.";
        exit();
    }

    // Validasi ukuran maksimal 2MB
    if ($_FILES["foto"]["size"] > 2097152) {
        echo "Ukuran file terlalu besar! Maksimal 2MB.";
        exit();
    }

    // Pindahkan file ke folder uploads
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        echo "Gagal mengunggah foto!";
        exit();
    }

    // Ambil foto lama untuk dihapus
    $sql_foto = "SELECT foto_profil FROM users WHERE id = ?";
    $stmt_foto = $conn->prepare($sql_foto);
    $stmt_foto->bind_param("i", $user_id);
    $stmt_foto->execute();
    $result_foto = $stmt_foto->get_result();
    $row_foto = $result_foto->fetch_assoc();

    // Hapus foto lama jika ada
    if (!empty($row_foto['foto_profil']) && file_exists("../uploads/userImg/" . $row_foto['foto_profil'])) {
        unlink("../uploads/userImg/" . $row_foto['foto_profil']);
    }
}

// Update database
$sql = "UPDATE users SET nama=?, email=?, hp=?, alamat=?, username=?";
if ($foto_nama) {
    $sql .= ", foto_profil=?";
}
$sql .= " WHERE id=?";

$stmt = $conn->prepare($sql);
if ($foto_nama) {
    $stmt->bind_param("ssssssi", $nama, $email, $hp, $alamat, $username, $foto_nama, $user_id);
} else {
    $stmt->bind_param("sssssi", $nama, $email, $hp, $alamat, $username, $user_id);
}

if ($stmt->execute()) {
    echo "Profil berhasil diperbarui!";
} else {
    echo "Gagal memperbarui profil!";
}

$stmt->close();
$conn->close();
?>
