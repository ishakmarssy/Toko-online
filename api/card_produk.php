<div class="col">
                    <div class="card h-100 <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>" aria-hidden="true">
                        <div class="position-relative">
                            <!-- Detail Produk -->
                            <a href="api/detail_produk.php?id=<?= $row["id"]; ?>"
                                class="text-decoration-none text-dark detail-produk <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>">

                                <!-- Gambar Produk -->
                                <img src="uploads/<?= htmlspecialchars($row["gambar"]); ?>" class="card-img-top <?= ($row["stok"] <= 0) ? 'habis' : ''; ?>" alt="<?= htmlspecialchars($row["nama"]); ?>">
                            </a>

                            <!-- Overlay "Stok Habis" -->
                            <?php if ($row["stok"] <= 0): ?>
                                <div class="stok-overlay">Habis</div>
                            <?php endif; ?>

                            <!-- Overlay "Promo" jika harga lama tersedia -->
                            <?php if ($row["harga_lama"] > 0): ?>
                                <img src="uploads/banner/promo.png" class="promo-overlay" alt="Promo">
                                <div class="promo-overlay <?= ($row['stok'] <= 0) ? 'habis' : ''; ?>"></div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Nama Produk -->
                            <h8 class="card-title text-black placeholder-glow"><?= htmlspecialchars($row["nama"]); ?></h8>
                            <hr>

                            <!-- Harga Produk -->
                            <p class="card-text text-danger placeholder-glow mb-0"><?php if ($row["harga_lama"] > 0): ?>
                                    <span class="text-muted harga-lama"><s>Rp.<?= number_format($row["harga_lama"], 0, ',', '.'); ?></s>
                                    </span>
                                <?php endif; ?>
                                Rp.<?= number_format($row["harga"], 0, ',', '.'); ?>
                            </p>
                            <p class="card-desk placeholder-glow mb-1"> <?= substr($row['deskripsi'], 0, 20); ?>... </p>

                            
                        <?php if (isset($_SESSION["user_id"])): ?>

                            <!-- Tombol untuk membuka modal -->
                            <!-- Tombol untuk membuka modal -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                            
                            <!-- Stok Produk -->
                            <h8 class="card-text text-black placeholder-glow"><small class="text-muted">Stok: <?= $row['stok']; ?></small></h8>
                            <button class="btn btn-toko justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#modalKeranjang<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                                <i class="bi bi-cart-plus"></i>
                            </button>
</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah ke Keranjang -->
<div class="modal fade" id="modalKeranjang<?= $row["id"]; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row["id"]; ?>" aria-hidden="true">
    <div class="modal-dialog modal-fullwidth">
        <div class="modal-content">
            <div class="modal-header bg-white text-Black">
                <h5 class="modal-title" id="modalLabel<?= $row["id"]; ?>">Tambah ke Keranjang</h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Gambar Produk -->
                <img src="uploads/<?= htmlspecialchars($row["gambar"]); ?>" class="card-img-top <?= ($row["stok"] <= 0) ? 'habis' : ''; ?>" alt="<?= htmlspecialchars($row["nama"]); ?>">
                            </a>

                <p class="mb-2 fw-semibold"><?= htmlspecialchars($row["nama"]); ?></p>
                <p class="text-muted small">Stok tersedia: <?= $row['stok']; ?></p>

                <!-- Form Tambah ke Keranjang -->
                <form class="form-keranjang" data-id="<?= $row["id"]; ?>">
                    <input type="hidden" name="produk_id" value="<?= $row["id"]; ?>">

                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <button class="btn btn-outline-secondary minus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>-</button>
                        <span class="jumlah-text mx-3 fw-bold fs-5" data-id="<?= $row['id']; ?>">1</span>
                        <button class="btn btn-outline-secondary plus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>+</button>
                    </div>

                    <input type="hidden" name="jumlah" class="jumlah-hidden" id="jumlah-<?= $row["id"]; ?>" value="1" max="<?= $row['stok']; ?>">

                    <button type="submit" class="btn btn-success w-100 d-flex justify-content-center align-items-center" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                        <i class="bi bi-cart-plus fs-5 me-2"></i> Tambahkan ke Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>