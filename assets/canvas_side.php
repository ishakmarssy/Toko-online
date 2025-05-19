<!-- Offcanvas -->
<div class="offcanvas offcanvas-start  custom-offcanvas"  tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel"><?php echo htmlspecialchars($store_name); ?> <i class="bi bi-bag-check-fill"></i></h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
    <div class="offcanvas-body">
      <div class="list-group">
        <!-- Bagian kanan: Link Navigasi -->
        <?php if ($loggedIn): ?>
          <a class="nav-link" href="api/status_pesanan.php">
          <i class="material-icons oc-icon">history</i> Status Pesanan
          </a>
          <a class="nav-link" href="assets/keranjang.php">
          <i class="material-icons oc-icon">shopping_cart</i> Keranjang
          </a>
          <a class="nav-link" href="perbaikan.html">
          <i class="material-icons oc-icon">settings</i> Pengaturan
          </a>
          <a class="nav-link" href="assets/profil.php">
          <i class="material-icons oc-icon">account_circle</i> Profil
          </a>
          <a class="nav-link text-danger" href="assets/logout.php">
            <i class="bi bi-box-arrow-right oc-icon" style="font-size: 1.5rem;"></i> Keluar
          </a>
          <?php else: ?>
          <div class="d-flex align-items-center">
            <a class="btn btn-outline-success me-2" href="assets/login.php"><i class="bi bi-box-arrow-in-right"></i> Masuk</a>
            <a class="btn btn-success" href="assets/register.php">Daftar</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
</div>