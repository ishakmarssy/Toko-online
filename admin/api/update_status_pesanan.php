<?php
include("../db/kon.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
    $status = isset($_POST["status"]) ? $_POST["status"] : '';

    if (!$id || empty($status)) {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        exit;
    }

    // Cek status pesanan sebelumnya
    $query = "SELECT status FROM pesanan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $previousStatus = $result->fetch_assoc()['status'];
    $stmt->close();

    // Jika status sebelumnya sama-sama "gagal" atau "dibatalkan", hentikan proses
    if (($previousStatus === "gagal" || $previousStatus === "dibatalkan") &&
        ($status === "gagal" || $status === "dibatalkan")) {
        echo json_encode(["success" => true, "message" => "Status tidak perlu diubah"]);
        exit;
    }

    // Ambil detail pesanan untuk mendapatkan produk dan jumlahnya
    $query = "SELECT produk_id, jumlah FROM pesanan_detail WHERE pesanan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produkList = [];

    while ($row = $result->fetch_assoc()) {
        $produkList[] = $row;
    }
    $stmt->close();

    // Mulai transaksi agar update status dan stok berjalan aman
    $conn->begin_transaction();

    // Update status pesanan
    $stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if (!$stmt->execute()) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Gagal mengupdate status"]);
        exit;
    }
    $stmt->close();

    // Jika status berubah ke "gagal" atau "dibatalkan", kembalikan stok
    if ($status === "gagal" || $status === "dibatalkan") {
        foreach ($produkList as $produk) {
            $produk_id = $produk['produk_id'];
            $jumlah = $produk['jumlah'];

            $stmt = $conn->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?");
            $stmt->bind_param("ii", $jumlah, $produk_id);

            if (!$stmt->execute()) {
                $conn->rollback();
                echo json_encode(["success" => false, "message" => "Gagal mengembalikan stok"]);
                exit;
            }
            $stmt->close();
        }
    }

    // Commit transaksi
    $conn->commit();
    echo json_encode(["success" => true]);
}

?>
