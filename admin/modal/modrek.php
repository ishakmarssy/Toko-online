<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-square"></i> Tambah Metode Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../admin/api/tmbh_rek.php" method="POST">
                <div class="modal-body">

                    <div class="container text-center">
                        <div class="row">
                            <div class="col">
                                <label>Via:</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Bank - Rekening:</label>
                                <input type="text" name="nomor_rekening" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                </div>
            </form>
            </br>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../admin/api/edt_rek.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" value="<?= $row['id']; ?>">

                    <label>Nama Metode:</label>
                    <input type="text" name="nama" id="edit-nama" class="form-control" value="<?= $row['nama']; ?>" required>

                    <label>Nomor Rekening:</label>
                    <input type="text" name="nomor_rekening" id="edit-norek" class="form-control" value="<?= $row['nomor_rekening']; ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>