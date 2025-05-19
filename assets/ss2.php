<div id="produk" class="py-5" style="height: auto;">
    <div class="card p-1">
        <div class="container mt-3 text-center">
            <h3 class="text-center"><i class="bi bi-clipboard-check"></i> Produk</h3>
            <button type="button" class="btn btn-sm btn-success bi-plus-circle mb-2" data-bs-toggle="modal" data-bs-target="#inputProdukModal">
                Tambah Produk
            </button>

            <style>
                .produk-card {
                    width: 200px;
                    font-size: 13px;
                    margin: 2px;

                }

                .produk-card img {
                    width: auto;
                    height: 150px;
                    object-fit: cover;
                    border-top-left-radius: 5px;
                    border-top-right-radius: 5px;
                }

                .produk-card .card-body {
                    padding: 5px;
                }

                .produk-card .card-title {
                    font-size: 14px;
                    text-align: left;
                }

                .produk-card .card-text {
                    font-size: 12px;
                    text-align: left;
                    margin-bottom: 2px;
                }

                .produk-card .harga-lama {
                    text-decoration: line-through;
                    color: gray;
                }

                .produk-card .btn {
                    font-size: 11px;
                    padding: 4px 6px;
                }

                .produk-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    gap: 3px;
                    margin: 0px
                }
            </style>

            <!-- Grid Produk -->
            <div class="produk-container">
                <?php
                include 'kon.php';
                //session_start();
                $sql = "SELECT * FROM produk ORDER BY id DESC";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                ?>
                    <div class="produk-card card shadow-sm">
                        <img src="../uploads/<?= $row['gambar']; ?>" class="card-img-top" alt="<?= $row['nama']; ?>">
                        <div class="card-body">
                            <h6 class="card-title"> <?= $row['nama']; ?> </h6>
                            <p class="card-text harga-lama">Rp<?= number_format($row['harga_lama'], 0, ',', '.'); ?></p>
                            <p class="card-text text-danger font-weight-bold">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></p>
                            <p class="card-text"> <?= substr($row['deskripsi'], 0, 15); ?>... </p>
                            <p class="card-text"><small class="text-muted">Stok: <?= $row['stok']; ?></small></p>
                            <div class="d-flex justify-content-start gap-2">
                                <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProdukModal<?= $row['id']; ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusProdukModal<?= $row['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                    include("modal/modal1.php");
                    include("modal/modal2.php");
                    ?>
                <?php } ?>
            </div>
        </div>
        <?php include("modal/modal3.php"); ?>
    </div>
</div>