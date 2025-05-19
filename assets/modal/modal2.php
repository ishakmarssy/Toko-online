<!-- Modal Hapus Produk -->
<div class="modal fade" id="hapusProdukModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus produk <strong><?= htmlspecialchars($row['nama']); ?></strong>?</p>
            </div>
            <div class="modal-footer">
            <form action="dell.php" method="GET">
    <input type="hidden" name="id" value="<?= $row['id']; ?>">
    <button type="submit" class="btn btn-danger">Hapus</button>
</form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
