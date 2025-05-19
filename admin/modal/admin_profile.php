<!-- Modal Profil -->
<div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profilModalLabel"><i class="bi bi-person-circle"></i> Profil Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <img src="../uploads/<?= $data_admin['foto'] ?? 'default.jpg'; ?>" alt="Foto Admin" class="rounded-circle img-thumbnail" width="100">
        </div>
        <table class="table table-borderless">
          <tr>
            <th>Nama</th>
            <td>: <?= $data_admin['nama']; ?></td>
          </tr>
          <tr>
            <th>Username</th>
            <td>: <?= $data_admin['username']; ?></td>
          </tr>
          <tr>
            <th>Email</th>
            <td>: <?= $data_admin['email']; ?></td>
          </tr>
          <tr>
            <th>Nomor WA</th>
            <td>: <?= $data_admin['no_wa']; ?></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Tutup</button>
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#pengaturanWAModal">
  <i class="bi bi-whatsapp"></i> Ubah No. WA
</button>

      </div>
    </div>
  </div>
</div>