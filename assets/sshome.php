<div id="pesanan" class="py-5" style="height: auto;">
    <div class="card p-1">
        <div class="container mt-3">
            <h3 class="text-center"><i class="bi bi-clipboard-check"></i> Pesanan</h3>
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <button id="hapusSemuaSelesai" class="btn btn-sm btn-dark mb-3"><i class="bi bi-trash"></i> Pesanan Selesai</button>
                    </div>
                    <div class="col">
                        <button id="hapusSemuaGagal" class="btn btn-sm btn-danger mb-3"><i class="bi bi-trash"></i> Pesanan Gagal</button>
                    </div>
                    <div class="col">
                        <button id="hapusSemuaDibatalkan" class="btn btn-sm btn-dark mb-3"><i class="bi bi-trash"></i> Pesanan Dibatalkan</button>
                    </div>
                </div>
            </div>

            <?php

            // Inisialisasi array status dengan nilai default 0
            $status_counts = [
                'pending' => 0,
                'diproses' => 0,
                'dikirim' => 0,
                'selesai' => 0,
                'gagal' => 0,
                'dibatalkan' => 0
            ];

            // Ambil jumlah pesanan berdasarkan status
            $query = "SELECT status, COUNT(*) AS jumlah FROM pesanan GROUP BY status";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = strtolower($row['status']); // Pastikan status dalam format yang sesuai
                    if (array_key_exists($status, $status_counts)) {
                        $status_counts[$status] = $row['jumlah'];
                    }
                }
            }
            ?>

            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Status</th>
                        <th>Jumlah Pesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-hourglass-split"></i> Pending</td>
                        <td><?= isset($status_counts['pending']) ? $status_counts['pending'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=pending" class="btn btn-warning btn-sm">Lihat Pesanan</a></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-gear"></i> Diproses</td>
                        <td><?= isset($status_counts['diproses']) ? $status_counts['diproses'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=diproses" class="btn btn-primary btn-sm">Lihat Pesanan</a></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-truck"></i> Dikirim</td>
                        <td><?= isset($status_counts['dikirim']) ? $status_counts['dikirim'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=dikirim" class="btn btn-info btn-sm">Lihat Pesanan</a></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-check-circle"></i> Selesai</td>
                        <td><?= isset($status_counts['selesai']) ? $status_counts['selesai'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=selesai" class="btn btn-success btn-sm">Lihat Pesanan</a></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-x-circle"></i> Dibatalkan</td>
                        <td><?= isset($status_counts['dibatalkan']) ? $status_counts['dibatalkan'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=dibatalkan" class="btn btn-danger btn-sm">Lihat Pesanan</a></td>
                        
                    </tr>
                    <tr>
                        <td><i class="bi bi-x-circle"></i> Gagal</td>
                        <td><?= isset($status_counts['gagal']) ? $status_counts['gagal'] : 0; ?></td>
                        <td><a href="kelola_pesanan.php?status=gagal" class="btn btn-danger btn-sm">Lihat Pesanan</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>// Event untuk menghapus semua pesanan dengan status "Selesai"
    $(document).on("click", "#hapusSemuaSelesai", function() {
        if (confirm("Apakah Anda yakin ingin menghapus semua pesanan yang sudah selesai?")) {
            $.ajax({
                url: "../api/hapus_semua_selesai.php",
                type: "POST",
                success: function(response) {
                    alert("Semua pesanan selesai berhasil dihapus!");
                    pesananData = pesananData.filter(row => row.status.toLowerCase() !== "selesai"); // Hapus dari array
                    renderPesanan(currentPage); // Refresh tampilan
                },
                error: function() {
                    alert("Gagal menghapus pesanan.");
                }
            });
        }
    });

    // Event untuk menghapus semua pesanan dengan status "Gagal"
    $(document).on("click", "#hapusSemuaGagal", function() {
        if (confirm("Apakah Anda yakin ingin menghapus semua pesanan yang Gagal?")) {
            $.ajax({
                url: "../api/hapus_semua_gagal.php",
                type: "POST",
                success: function(response) {
                    alert("Semua pesanan yang gagal berhasil dihapus!");
                    pesananData = pesananData.filter(row => row.status.toLowerCase() !== "gagal"); // Hapus dari array
                    renderPesanan(currentPage); // Refresh tampilan
                },
                error: function() {
                    alert("Gagal menghapus pesanan.");
                }
            });
        }
    });

    // Event untuk menghapus semua pesanan dengan status "Dibatalkan"
    $(document).on("click", "#hapusSemuaDibatalkan", function() {
        if (confirm("Apakah Anda yakin ingin menghapus semua pesanan yang Diabatalkan?")) {
            $.ajax({
                url: "../api/hapus_semua_dibatalkan.php",
                type: "POST",
                success: function(response) {
                    alert("Semua pesanan yang dibatalkan berhasil dihapus!");
                    pesananData = pesananData.filter(row => row.status.toLowerCase() !== "dibatalkan"); // Hapus dari array
                    renderPesanan(currentPage); // Refresh tampilan
                },
                error: function() {
                    alert("Gagal menghapus pesanan.");
                }
            });
        }
    });
    </script>
