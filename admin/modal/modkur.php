<!-- Modal Tambah -->
<div class="modal fade" id="tambahKurModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-square"></i> Jasa Pengantaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </button>

            </div>
            <form action="../admin/api/tmbh_kurir.php" method="POST">
                <div class="modal-body">


                    <div class="container text-center">
                        <div class="row">
                            <div class="col">
                                <label>Area:</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Biaya:</label>
                                <input type="text" name="biaya" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                </div>
            </form>
            </br>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editKurModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../admin/api/edt_kurir.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-idk" value="<?= $row['id']; ?>">

                    <div class="container text-center">

                        <div class="row">
                            <div class="col">
                                <label>Area:</label>
                                <input
                                    type="text"
                                    name="nama"
                                    id="edit-namak"
                                    class="form-control"
                                    value="<?= $row['nama']; ?>"
                                    required>
                            </div>
                            <div class="col">
                                <label>Biaya:</label>
                                <input
                                    type="text"
                                    name="biaya"
                                    id="edit-biaya"
                                    class="form-control"
                                    value="<?= $row['biaya']; ?>"
                                    required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>