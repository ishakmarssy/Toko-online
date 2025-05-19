<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow-sm">
  <div class="container">
    <div class="d-flex align-items-center w-100 justify-content-between">

      <!-- Brand-->
      <!-- <a class="navbar-brand" href="index.php">
        <?php echo htmlspecialchars($store_name); ?>
        <i class="bi bi-bag-check-fill"></i> -->
        

      <!-- Search Form -->
      <form class="flex-grow-1 mx-1 me-5" action="api/search.php" method="GET">
        <div class="input-group search-wrapper">
          <input type="search" class="form-control" name="q" placeholder="Cari produk..." aria-label="Search">
          <button class="btn btn-cari" type="submit">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>

      <!-- Keranjang -->
      <a href="assets/keranjang.php" class="navbar-brand position-relative">
        <i class="bi bi-cart3 fs-4 text-white"></i>
        <span id="cart-count" class="position-absolute top-0 start-0 translate-middle badge rounded-pill">
          0
        </span>
      </a>
      <!-- Akun --> 
      <a href="assets/profil.php" class="navbar-brand position-relative me-3">
        <i class="bi bi-person-circle fs-4 text-white"></i>
        </a>
        
    </div>
  </div>
</nav>
