<?php
include 'kon.php';

// Ambil halaman saat ini dari request AJAX, default ke halaman 1
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$produk_per_halaman = 6;
$offset = ($halaman - 1) * $produk_per_halaman;

// Ambil total jumlah produk
$result_total = $conn->query("SELECT COUNT(*) AS total FROM produk");
$total_produk = $result_total->fetch_assoc()['total'];
$total_halaman = ceil($total_produk / $produk_per_halaman);

// Ambil data produk berdasarkan halaman, urutkan dari yang terbaru
$sql = "SELECT * FROM produk ORDER BY created_at DESC LIMIT $produk_per_halaman OFFSET $offset";
$result = $conn->query($sql);

$produk_html = '';
while ($row = $result->fetch_assoc()) {
    $produk_html .= '
    <div class="produk-card card shadow-sm">
        <img src="../uploads/' . htmlspecialchars($row['gambar']) . '" class="card-img-top" alt="' . htmlspecialchars($row['nama']) . '">
        <div class="card-body">
            <h6 class="card-title">' . htmlspecialchars($row['nama']) . '</h6>
            <p class="card-text harga-lama">Rp' . number_format($row['harga_lama'], 0, ',', '.') . '</p>
            <p class="card-text text-danger font-weight-bold">Rp' . number_format($row['harga'], 0, ',', '.') . '</p>
            <p class="card-text">' . substr(htmlspecialchars($row['deskripsi']), 0, 30) . '...</p>
            <p class="card-text"><small class="text-muted">Stok: ' . htmlspecialchars($row['stok']) . '</small></p>
            <div class="d-flex justify-content-start gap-2">
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProdukModal' . $row['id'] . '">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusProdukModal' . $row['id'] . '">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>';
}

// Buat pagination
$pagination_html = '';
if ($total_halaman > 1) {
    for ($i = 1; $i <= $total_halaman; $i++) {
        $active = ($i == $halaman) ? 'active' : '';
        $pagination_html .= '<li class="page-item ' . $active . '">
                                <a href="#" class="page-link" data-page="' . $i . '">' . $i . '</a>
                             </li>';
    }
}

// Kirim data dalam format JSON
echo json_encode(['produk' => $produk_html, 'pagination' => $pagination_html]);
?>
