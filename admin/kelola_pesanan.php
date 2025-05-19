<?php
//session_start();
include("db/kon.php");
include("../assets/function.php");

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Ambil status dari URL jika ada
$status_filter = isset($_GET['status']) ? $_GET['status'] : "";

$query = "SELECT p.id, u.nama AS nama_user, p.alamat, 
                 COALESCE(pe.nama, 'Tidak Diketahui') AS metode_pengiriman, 
                 COALESCE(m.nama, 'Tidak Diketahui') AS metode_pembayaran, 
                 p.total_harga, p.status, p.created_at 
          FROM pesanan p
          LEFT JOIN users u ON p.user_id = u.id
          LEFT JOIN pengiriman pe ON p.pengiriman_id = pe.id
          LEFT JOIN metode_pembayaran m ON p.metode_pembayaran_id = m.id";

if ($status_filter !== "") {
    $query .= " WHERE p.status = '" . $conn->real_escape_string($status_filter) . "'";
}

$query .= " ORDER BY p.created_at DESC";

$result = $conn->query($query);
$pesananData = [];

while ($row = $result->fetch_assoc()) {
    $pesananData[] = [
        "id" => htmlspecialchars($row["id"]),
        "nama_user" => htmlspecialchars($row["nama_user"]),
        "alamat" => htmlspecialchars($row["alamat"]),
        "metode_pengiriman" => htmlspecialchars($row["metode_pengiriman"]),
        "metode_pembayaran" => htmlspecialchars($row["metode_pembayaran"]),
        "total_harga" => "Rp" . number_format($row["total_harga"]),
        "status" => htmlspecialchars($row["status"]),
        "created_at" => htmlspecialchars($row["created_at"])
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Pesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            padding: 0px;
            font-size: 0.8rem;
            gap: 8px;
        }

        .card-title {
            font-size: 1rem;
        }
        .update-status{
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div id="pesanan" class="py-5">
    <div class="card p-auto">
        <div class="container mt-4">
            <h3 class="text-center"><i class="bi bi-clipboard-check"></i> Kelola Pesanan</h3>

            <!-- Filter Status -->
            <div class="mb-3">
                <label for="statusFilter" class="form-label"><strong>Filter Status</strong></label>
                <select id="statusFilter" class="form-select update-status">
                    <option value="" <?= $status_filter === "" ? "selected" : "" ?>>Semua Status</option>
                    <option value="pending" <?= $status_filter === "pending" ? "selected" : "" ?>>Pending</option>
                    <option value="diproses" <?= $status_filter === "diproses" ? "selected" : "" ?>>Diproses</option>
                    <option value="dikirim" <?= $status_filter === "dikirim" ? "selected" : "" ?>>Dikirim</option>
                    <option value="selesai" <?= $status_filter === "selesai" ? "selected" : "" ?>>Selesai</option>
                    <option value="gagal" <?= $status_filter === "gagal" ? "selected" : "" ?>>Gagal</option>
                    <option value="dibatalkan" <?= $status_filter === "dibatalkan" ? "selected" : "" ?>>Dibatalkan</option>
                </select>
            </div>

            <input type="text" id="search-id" class="form-control mb-3 update-status" placeholder="Cari Invoice Pesanan">
            <div class="pesanan-container" id="pesanan-list"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var pesananData = <?= json_encode($pesananData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    $(document).ready(function() {
    function getStatusBadge(status) {
        let badgeClass = "";
        switch (status.toLowerCase()) {
            case "pending": badgeClass = "bg-warning text-dark"; break;
            case "diproses": badgeClass = "bg-primary"; break;
            case "dikirim": badgeClass = "bg-info"; break;
            case "selesai": badgeClass = "bg-success"; break;
            case "gagal": badgeClass = "bg-danger"; break;
            case "dibatalkan": badgeClass = "bg-secondary"; break;
            default: badgeClass = "bg-dark";
        }
        return `<span class="badge ${badgeClass}">${status}</span>`;
    }

    $(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const filterStatus = urlParams.get("status");

    function getAllowedStatuses(currentStatus) {
        let allowedStatuses = {
            "pending": ["diproses", "dikirim","selesai", "gagal"],
            "diproses": ["dikirim","selesai", "gagal"],
            "dikirim": ["selesai", "gagal"],
            "selesai": [],
            "gagal": [],
            "dibatalkan": []
        };
        return allowedStatuses[currentStatus] || [];
    }

    function renderPesanan(searchId = "", filterStatus = "") {
        var filteredData = pesananData.filter(row => {
            return (searchId === "" || row.id.includes(searchId)) &&
                   (filterStatus === "" || row.status.toLowerCase() === filterStatus.toLowerCase());
        });

        $("#pesanan-list").html("");

        if (filteredData.length === 0) {
            $("#pesanan-list").html("<div class='alert alert-danger text-center'>Pesanan tidak ditemukan.</div>");
            return;
        }

        filteredData.forEach(function(row) {
            let allowedStatuses = getAllowedStatuses(row.status.toLowerCase());
            let disabledSelect = (allowedStatuses.length === 0) ? "disabled" : "";

            let statusOptions = "";
            ["pending", "diproses", "dikirim", "selesai", "gagal", "dibatalkan"].forEach(status => {
                if (status === row.status.toLowerCase() || allowedStatuses.includes(status)) {
                    let selected = (status === row.status.toLowerCase()) ? "selected" : "";
                    statusOptions += `<option value="${status}" ${selected}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                }
            });

            var card = `<div class='card shadow-sm m-2 p-0'>
                            <div class='card-body'>
                                <h6 class='text-danger text-end'><strong>#${row.id}</strong></h6>
                                <p class="card-text mb-1"><strong>Nama:</strong> ${row.nama_user}</p>
                                <p class="card-text mb-1"><strong>Alamat:</strong> ${row.alamat}</p>
                                <p class="card-text mb-1"><strong>Pengiriman:</strong> ${row.metode_pengiriman}</p>
                                <p class="card-text mb-1"><strong>Pembayaran:</strong> ${row.metode_pembayaran}</p>
                                <p class="card-text mb-1"><strong>Total:</strong> ${row.total_harga}</p>
                                <p class="card-text mb-1"><strong>Tanggal:</strong> ${row.created_at}</p>
                                <p class="card-text mb-1"><strong>Status:</strong> <span id="status-badge-${row.id}">${getStatusBadge(row.status)}</span></p>
                                
                                <select class="form-select update-status" data-id="${row.id}" ${disabledSelect}>
                                    ${statusOptions}
                                </select>

                                <a href='../api/detail_pesanan.php?id=${row.id}' class='btn btn-danger btn-sm mt-2'>Detail Pesanan</a>
                            </div>
                        </div>`;
            $("#pesanan-list").append(card);
        });

        $(document).on("change", ".update-status", function() {
        let id = $(this).data("id");
        let newStatus = $(this).val();

        $.ajax({
            url: "api/update_status_pesanan.php",
            type: "POST",
            data: { id: id, status: newStatus },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    alert("Status berhasil diperbarui!");
                    $(`#status-badge-${id}`).text(newStatus);
                }
            },
            error: function() {
                alert("Terjadi kesalahan saat mengupdate status.");
            }
            });
        });
    }

    renderPesanan("", filterStatus);
});


    // Ambil status dari URL jika ada
    const urlParams = new URLSearchParams(window.location.search);
    const filterStatus = urlParams.get("status");
    if (filterStatus) {
        $("#statusFilter").val(filterStatus);
    }

    $("#search-id").on("input", function() {
        renderPesanan($(this).val(), $("#statusFilter").val());
    });

    $("#statusFilter").on("change", function() {
        window.location.href = "kelola_pesanan.php?status=" + $(this).val();
    });

    renderPesanan("", filterStatus);
});


</script>



</body>
</html>
