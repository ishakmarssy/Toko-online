<!-- Modal -->
<div class="modal fade" id="inputProdukModal" tabindex="-1" aria-labelledby="inputProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputProdukModalLabel"><i class="bi bi-plus-square"></i> Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                     <!-- Kategori -->
                     <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                    </div>

                    <!-- Harga, Harga Lama, dan Stok dalam satu baris -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="harga_lama" class="form-label">Harga Lama <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_lama" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Upload Gambar <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="gambar" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Simpan dan tampilkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
