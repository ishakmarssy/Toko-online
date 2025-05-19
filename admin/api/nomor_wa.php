<!-- Modal Pengaturan WhatsApp -->
<div class="modal fade" id="pengaturanWAModal" tabindex="-1" aria-labelledby="pengaturanWAModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="pengaturanWAModalLabel"><i class="bi bi-whatsapp"></i> Pengaturan Nomor WhatsApp</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <div class="modal-body">
        <?php if (isset($error_wa)): ?>
          <div class="alert alert-danger"><?php echo $error_wa; ?></div>
        <?php endif; ?>
        <?php if (isset($success_wa)): ?>
          <div class="alert alert-success"><?php echo $success_wa; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Nomor WhatsApp</label>
            <input type="text" name="no_wa" class="form-control" value="<?php echo htmlspecialchars($admin_data['no_wa'] ?? ''); ?>" required>
            <small class="text-muted">Masukkan nomor dengan format yang benar, contoh: 628123456789</small>
          </div>
          <div class="d-flex justify-content-end">
            <button type="submit" name="submit_wa" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
