<?php
session_start();
include("../assets/kon.php");


if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah Slider
if (isset($_POST['tambah'])) {
    $link = trim($_POST['link']);
    $gambar = $_FILES['gmbr'];

    $namaFile = basename($gambar['name']);
    $targetDir = "../uploads/";
    $targetFile = $targetDir . $namaFile;

    // Validasi ekstensi gambar
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['message'] = "Format gambar tidak valid. Hanya JPG, JPEG, PNG, GIF, WEBP yang diperbolehkan.";
    } elseif ($gambar['size'] > 2 * 1024 * 1024) { // maksimal 2MB
        $_SESSION['message'] = "Ukuran gambar terlalu besar. Maksimal 2MB.";
    } elseif (move_uploaded_file($gambar['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO slider (gmbr, link) VALUES (?, ?)");
        $stmt->bind_param("ss", $namaFile, $link);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Slider berhasil ditambahkan.";
    } else {
        $_SESSION['message'] = "Gagal mengupload gambar.";
    }

    header("Location: ../admin/index.php#slider");
    exit();
}


// Update Slider
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $link = $_POST['link'];
    
    if (!empty($_FILES['gmbr']['name'])) {
        $gambar = $_FILES['gmbr']['name'];
        $target = "../uploads/" . basename($gambar);
        move_uploaded_file($_FILES['gmbr']['tmp_name'], $target);
        $stmt = $conn->prepare("UPDATE slider SET gmbr = ?, link = ? WHERE id = ?");
        $stmt->bind_param("ssi", $gambar, $link, $id);
    } else {
        $stmt = $conn->prepare("UPDATE slider SET link = ? WHERE id = ?");
        $stmt->bind_param("si", $link, $id);
    }
    
    $stmt->execute();
    $stmt->close();
    $_SESSION['message'] = "Slider berhasil diperbarui.";
    header("Location: ../admin/index.php#slider");
    exit();
}

// Hapus Slider
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("SELECT gmbr FROM slider WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if (file_exists("../uploads/" . $row['gmbr'])) {
        unlink("../uploads/" . $row['gmbr']);
    }
    
    $stmt = $conn->prepare("DELETE FROM slider WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "Slider berhasil dihapus.";
    header("Location: ../admin/index.php#slider");
    exit();
}

$conn->close();
?>
