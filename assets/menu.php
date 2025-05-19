<!-- Menu Kategori dengan Icon Berwarna -->
<?php if (isset($_SESSION['user_nama'])): ?>
       <!-- <h5 class="text-center mb-4 ">Haii! <?= htmlspecialchars($_SESSION['user_nama']); ?></h5> -->
    <?php else: ?>
    <?php endif; ?>
    <div class="menu-bar d-flex">
            <a href="perbaikan.html" class="menu-item">
                <i class="bi bi-bicycle"></i>
                <strong>Motor</strong>
            </a>
            <a href="perbaikan.html" class="menu-item">
                <i class="bi bi-car-front-fill"></i>
                <strong>Mobil</strong>
            </a>
            <a href="perbaikan.html" class="menu-item">
                <i class="bi bi-truck"></i>
                <strong>Kurir</strong>
            </a>
            <a href="perbaikan.html" class="menu-item">
                <i class="bi bi-egg-fried"></i>
                <strong>Makanan</strong>
            </a>
            <a href="perbaikan.html" class="menu-item">
                <i class="bi bi-cup-straw"></i>
                <strong>Minuman</strong>
            </a>
        </div>