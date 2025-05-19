<div id="rekening" class="py-4">
    <div class="container mt-3">
        <div class="card shadow-sm p-4">
            <h4 class="mb-3 text-center"><i class="bi bi-clipboard-check"></i> Rekening & Pengiriman</h4>
            <div class="row">
                <!-- Card Rekening -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-center">Metode Pembayaran</h5>
                        <button class="btn btn-sm btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
                            <i class="bi bi-plus-circle"></i> Tambah Rekening
                        </button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center">
                                <thead class="table-success">
                                    <tr>
                                        <th>Via</th>
                                        <th>Rekening</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'kon.php';
                                    $sql = "SELECT * FROM metode_pembayaran";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['nama']; ?></td>
                                        <td><?= $row['nomor_rekening']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-edit"
                                                data-id="<?= $row["id"] ?>"
                                                data-nama="<?= $row["nama"] ?>"
                                                data-norek="<?= $row["nomor_rekening"] ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal<?= $row['id']; ?>"">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <a href="../admin/api/dell_rek.php?id=<?= $row["id"] ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Hapus metode ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    include ("modal/modrek.php");
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card Pengiriman -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <h5 class="text-center">Metode Pengiriman</h5>
                        <button class="btn btn-sm btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tambahKurModal">
                            <i class="bi bi-plus-circle"></i> Tambah Pengiriman
                        </button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center">
                                <thead class="table-success">
                                    <tr>
                                        <th>Area</th>
                                        <th>Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'kon.php';
                                    $sql = "SELECT * FROM pengiriman";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['nama']; ?></td>
                                        <td>Rp<?= number_format($row['biaya'], 0, ',', '.'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-kurir"
                                                data-id="<?= $row["id"] ?>"
                                                data-nama="<?= $row["nama"] ?>"
                                                data-biaya="<?= $row["biaya"] ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editKurModal<?= $row['id']; ?>"">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <a href="../admin/api/dell_kurir.php?id=<?= $row["id"] ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Hapus kurir ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    include ("modal/modkur.php");
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> <!-- End Row -->
        </div> <!-- End Card -->
    </div> <!-- End Container -->
</div>
