<div class="modal fade" id="modalNamaToko" tabindex="-1" aria-labelledby="modalNamaTokoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNamaTokoLabel">Pengaturan Nama Toko</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <?php if($error_store): ?>
          <div class="alert alert-danger"><?php echo $error_store; ?></div>
        <?php endif; ?>
        <?php if($success_store): ?>
          <div class="alert alert-success"><?php echo $success_store; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Nama Toko</label>
            <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($store_settings['store_name']); ?>">
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" name="submit_store" class="btn btn-success">Simpan Nama Toko</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
