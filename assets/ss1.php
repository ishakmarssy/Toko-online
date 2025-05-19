<div id="pesanan" class="py-5" style="height: auto;">
    <div class="card p-auto">
        <div class="container mt-4">
            <h3 class="text-center"><i class="bi bi-clipboard-check"></i> Pesanan</h3>

            <input type="text" id="search-id" class="form-control mb-3" placeholder="Cari Invoice Pesanan 123">
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <button id="hapusSemuaSelesai" class="btn btn-dark mb-3"><i class="bi bi-trash"></i> Pesanan Selesai</button>
                    </div>
                    <div class="col">
                        <button id="hapusSemuaGagal" class="btn btn-danger mb-3"><i class="bi bi-trash"></i> Pesanan Gagal</button>
                    </div>
                    <div class="col">
                        <button id="hapusSemuaDibatalkan" class="btn btn-dark mb-3"><i class="bi bi-trash"></i> Pesanan Dibatalkan</button>
                    </div>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <style>
    /* Layout utama */
    .pesanan-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }

    /* Kartu Pesanan */
    .pesanan-card {
        width: 100%;
        font-size: 14px;
        margin: 6px;
        padding: 15px;
        border-radius: 10px;
        border: none;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        background: white;
    }

    .pesanan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* Status Badge */
    .badge {
        font-size: 12px;
        padding: 5px 8px;
        border-radius: 8px;
        font-weight: bold;
    }

    .badge-pending {
        background: #ffc107;
        color: black;
    }

    .badge-diproses {
        background: #17a2b8;
        color: white;
    }

    .badge-dikirim {
        background: #007bff;
        color: white;
    }

    .badge-selesai {
        background: #28a745;
        color: white;
    }

    .badge-gagal {
        background: #dc3545;
        color: white;
    }

    .badge-dibatalkan {
        background: #6c757d;
        color: white;
    }

    /* Tombol Custom */
    .btn-custom {
        background: linear-gradient(to right, #d71149, #a00e39);
        color: white;
        font-weight: bold;
        font-size: 13px;
        padding: 7px 12px;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        border: none;
    }

    .btn-custom:hover {
        background: linear-gradient(to right, #b30e3a, #870c2e);
        transform: scale(1.05);
    }

    /* Search Input */
    .search-container {
        position: relative;
    }

    .search-container input {
        padding-left: 35px;
        border-radius: 8px;
    }

    .search-container i {
        position: absolute;
        left: 10px;
        top: 12px;
        color: gray;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .pagination a {
        padding: 8px 12px;
        margin: 3px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        background: #f1f1f1;
        transition: all 0.2s ease-in-out;
    }

    .pagination a:hover,
    .pagination .active {
        background-color: #d71149;
        color: white;
    }
</style>


                <div class="pesanan-container" id="pesanan-list"></div>
                <div class="pagination" id="pagination"></div>

            <?php else: ?>
                <div class="alert alert-warning text-center">Belum ada pesanan.</div>
            <?php endif; ?>
            <?php $stmt->close(); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var pesananData = [<?php while ($row = $result->fetch_assoc()): ?> {
                id: "<?= htmlspecialchars($row["id"]) ?>",
                nama_user: "<?= htmlspecialchars($row["nama_user"]) ?>",
                alamat: "<?= htmlspecialchars($row["alamat"]) ?>",
                metode_pengiriman: "<?= htmlspecialchars($row["metode_pengiriman"]) ?>",
                metode_pembayaran: "<?= htmlspecialchars($row["metode_pembayaran"]) ?>",
                total_harga: "Rp<?= number_format($row["total_harga"]) ?>",
                status: "<?= htmlspecialchars($row["status"]) ?>",
                created_at: "<?= htmlspecialchars($row["created_at"]) ?>"
            }, <?php endwhile; ?>];

        var perPage = 5,
            currentPage = 1;

        function renderPesanan(page, searchId = "") {
            var filteredData = searchId ? pesananData.filter(row => row.id.includes(searchId)) : pesananData;
            var start = (page - 1) * perPage,
                end = start + perPage,
                paginatedData = filteredData.slice(start, end);

            $("#pesanan-list").html("");
            if (paginatedData.length === 0) {
                $("#pesanan-list").html("<div class='alert alert-danger text-center'>Pesanan tidak ditemukan.</div>");
                $("#pagination").html("");
                return;
            }

            paginatedData.forEach(function(row) {
                var card = `<div class='pesanan-card card shadow-sm'><div class='card-body'>
                <h6 class='card-title text-danger text-end mb-1'><strong>#${row.id}</strong></h6>
                <p class='card-text mb-1'><strong>Nama:</strong> ${row.nama_user}</p>
                <p class='card-text mb-1'><strong>Alamat:</strong> ${row.alamat}</p>
                <p class='card-text mb-1'><strong>Pengiriman:</strong> ${row.metode_pengiriman}</p>
                <p class='card-text mb-1'><strong>Pembayaran:</strong> ${row.metode_pembayaran}</p>
                <p class='card-text mb-1'><strong>Total:</strong> ${row.total_harga}</p>
                <p class='card-text mb-1'><strong>Tanggal:</strong> ${row.created_at}</p>
                <div class='status-container'>
                    <select class='form-control status-select' data-id='${row.id}'>
                        <option value='Pending' ${row.status.toLowerCase() === 'pending' ? 'selected' : ''}>Menunggu Pembayaran</option>
                        <option value='Diproses' ${row.status.toLowerCase() === 'diproses' ? 'selected' : ''}>Diproses</option>
                        <option value='Dikirim' ${row.status.toLowerCase() === 'dikirim' ? 'selected' : ''}>Dikirim</option>
                        <option value='Selesai' ${row.status.toLowerCase() === 'selesai' ? 'selected' : ''}>Selesai</option>
                        <option value='Gagal' ${row.status.toLowerCase() === 'gagal' ? 'selected' : ''}>Gagal</option>
                        <option value='Dibatalkan' ${row.status.toLowerCase() === 'dibatalkan' ? 'selected' : ''}>Dibatalkan</option>
                    </select>
                   <a href='../api/detail_pesanan.php?id=${row.id}' class='btn btn-custom btn-sm'>Detail Pesanan</a>
                </div>
            </div></div>`;
                $("#pesanan-list").append(card);
            });

            renderPagination(filteredData.length);
        }

        function renderPagination(totalItems) {
            var totalPages = Math.ceil(totalItems / perPage),
                paginationHTML = "";
            for (var i = 1; i <= totalPages; i++) {
                paginationHTML += `<a href='#' class='${i === currentPage ? "active" : ""}' data-page='${i}'>${i}</a>`;
            }
            $("#pagination").html(paginationHTML);
        }

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data("page"));
            renderPesanan(currentPage, $("#search-id").val());
        });

        $("#search-id").on("input", function() {
            currentPage = 1;
            renderPesanan(currentPage, $(this).val());
        });

        renderPesanan(currentPage);
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on("change", ".status-select", function() {
            var pesananId = $(this).data("id"),
                statusBaru = $(this).val();
            $.ajax({
                url: "../api/proses_status.php",
                type: "POST",
                data: {
                    id: pesananId,
                    status: statusBaru
                },
                success: function() {
                    alert("Status berhasil diperbarui!");
                },
                error: function() {
                    alert("Gagal memperbarui status.");
                }
            });
        });
    });

    // Event untuk menghapus semua pesanan dengan status "Selesai"
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