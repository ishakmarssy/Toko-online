<!-- Modal Edit Produk -->
<div class="modal fade" id="editProdukModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title"><i class="bi bi-pencil-fill"></i> Edit Produk</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <form action="update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">

                    <div class="mb-2">
                        <label class="form-label mb-0">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="nama" value="<?= $row['nama']; ?>" required>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-2">
                        <label class="form-label mb-0">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="makanan" <?= $row['kategori'] == 'makanan' ? 'selected' : ''; ?>>Makanan</option>
                            <option value="minuman" <?= $row['kategori'] == 'minuman' ? 'selected' : ''; ?>>Minuman</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label mb-0">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="stok" value="<?= $row['stok']; ?>" required>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label mb-0">Harga Lama <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="harga_lama" value="<?= $row['harga_lama']; ?>" required>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label mb-0">Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" name="harga" value="<?= $row['harga']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-0">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" name="deskripsi" rows="2" required><?= $row['deskripsi']; ?></textarea>
                    </div>

                    <div class="mb-2 text-center">
                        <label class="form-label mb-0">Gambar Saat Ini <span class="text-danger">*</span></label><br>
                        <img src="../uploads/<?= $row['gambar']; ?>" class="img-thumbnail img-fluid" width="120">
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-0">Upload Gambar Baru</label>
                        <input type="file" class="form-control form-control-sm" name="gambar">
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-success btn-sm me-2">
                        <i class="bi bi-cloud-upload"></i> Update
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
