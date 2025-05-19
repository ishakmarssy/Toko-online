<?php
include("kon.php");

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// Cek apakah ada file yang diunggah
if (isset($_POST['upload']) && isset($_FILES['foto_profil'])) {
    $file = $_FILES['foto_profil'];
    $fileName = basename($file['name']);
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Validasi ekstensi file
    if (!in_array($fileExt, $allowedExt)) {
        echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, PNG, dan GIF.'); window.location='profil.php';</script>";
        exit();
    }

    // Validasi ukuran file
    if ($fileSize > $maxFileSize) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.location='profil.php';</script>";
        exit();
    }

    // Buat nama unik untuk file
    $newFileName = "profil_" . $user_id . "_" . time() . "." . $fileExt;
    $uploadPath = "../uploads/userImg/" . $newFileName;

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Simpan nama file di database
        $sql = "UPDATE users SET foto_profil = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newFileName, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Foto profil berhasil diunggah!'); window.location='profil.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan foto di database.'); window.location='profil.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Gagal mengunggah foto!'); window.location='profil.php';</script>";
    }
} else {
    echo "<script>alert('Tidak ada file yang dipilih!'); window.location='profil.php';</script>";
}

$conn->close();
?>
