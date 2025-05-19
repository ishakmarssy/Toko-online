
<!-- Filter Produk 
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-left"><i class="bi bi-tags"></i> Filter</h3>
        <select id="filterHarga" class="form-select w-auto">
            <option value="default">Semua</option>
            <option value="harga_lama">Promo</option>
            <option value="harga_terendah">Murah</option>
        </select>
    </div>
</div> -->
<!-- Produk -->
<div class="container mt-5">
    <h3 class="text-left mb-3 "><i class="bi bi-tags"></i> Promo</h3>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php
        include("assets/kon.php");

        $sql = "SELECT * FROM produk WHERE harga_lama > 0 ORDER BY id DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col">

                    <div class="card h-100 <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>" aria-hidden="true">
                        <div class="position-relative">
                            <!-- Detail Produk -->
                            <a href="api/detail_produk.php?id=<?= $row["id"]; ?>"
                                class="text-decoration-none text-dark detail-produk <?= ($row['stok'] <= 0) ? 'disabled-link' : ''; ?>">

                                <!-- Gambar Produk -->
                                <img src="uploads/<?= htmlspecialchars($row["gambar"]); ?>" class="card-img-top <?= ($row["stok"] <= 0) ? 'habis' : ''; ?>" alt="<?= htmlspecialchars($row["nama"]); ?>">
                            </a>

                            <!-- Overlay "Stok Habis" -->
                            <?php if ($row["stok"] <= 0): ?>
                                <div class="stok-overlay">Habis</div>
                            <?php endif; ?>

                            <!-- Overlay "Promo" jika harga lama tersedia -->
                            <?php if ($row["harga_lama"] > 0): ?>
                                <img src="uploads/banner/promo.png" class="promo-overlay" alt="Promo">
                                <div class="promo-overlay <?= ($row['stok'] <= 0) ? 'habis' : ''; ?>"></div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Nama Produk -->
                            <h8 class="card-title text-black placeholder-glow"><?= htmlspecialchars($row["nama"]); ?></h8>
                            <hr>

                            <!-- Harga Produk -->
                            <p class="card-text text-danger placeholder-glow"><?php if ($row["harga_lama"] > 0): ?>
                                    <span class="text-muted harga-lama"><s>Rp.<?= number_format($row["harga_lama"], 0, ',', '.'); ?></s>
                                    </span>
                                <?php endif; ?>
                                Rp.<?= number_format($row["harga"], 0, ',', '.'); ?>
                            </p>
                            <p class="card-text placeholder-glow"> <?= substr($row['deskripsi'], 0, 20); ?>... </p>
                            <!-- Stok Produk -->
                            <h8 class="card-text text-black placeholder-glow"><small class="text-muted">Stok: <?= $row['stok']; ?></small></h8>
                            <?php if (isset($_SESSION["user_id"])): ?>
                            <div class="d-flex align-items-center gap-1">
                                <!-- Form Tambah ke Keranjang -->
                                <form class="form-keranjang d-flex align-items-center" data-id="<?= $row["id"]; ?>">
                                    <input type="hidden" name="produk_id" value="<?= $row["id"]; ?>">
                                    <div class="input-group">
                                        <button class="btn btn-light minus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>-</button>
                                        <span class="jumlah-text" data-id="<?= $row['id']; ?>">1</span> 
                                        <button class="btn btn-light plus-btn" type="button" data-id="<?= $row['id']; ?>" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>+</button>
                                    </div>
                                    <input type="hidden" name="jumlah" class="jumlah-hidden" id="jumlah-<?= $row["id"]; ?>" value="1" max="<?= $row['stok']; ?>">
                                    <button  type="submit" class="btn btn-toko btn-tambah d-flex justify-content-center align-items-center"
                                        style="width: 35px; height: 35px;" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-cart-plus fs-3"></i>
                                    </button>
                                </form>

                                <!-- Form Beli Sekarang
                                <form action="assets/beli.php" method="POST">
                                    <input type="hidden" name="produk_id" value="<?= $row["id"]; ?>">
                                    <input type="hidden" name="jumlah" class="beli-jumlah" id="beli-jumlah-<?= $row["id"]; ?>" value="1">
                                    <button type="submit" class="btn btn-toko btn-beli d-flex justify-content-center align-items-center"
                                        style="width: 35px; height: 35px;" <?= ($row['stok'] <= 0) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-bag-check fs-3"></i>
                                    </button>
                                </form> -->
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="text-center">Tidak ada produk tersedia.</p>';
        }
        $conn->close();
        ?>
    </div>
</div>
<br>
<hr>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.minus-btn').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-id');
                let jumlahSpan = document.querySelector(`.jumlah-text[data-id="${productId}"]`);
                let jumlahHidden = document.querySelector(`#jumlah-${productId}`);
                let min = 1;
                let currentValue = parseInt(jumlahSpan.innerText) || min;

                if (currentValue > min) {
                    jumlahSpan.innerText = currentValue - 1;
                    jumlahHidden.value = currentValue - 1;
                    updateBeliJumlah(productId, currentValue - 1);
                }
            });
        });

        document.querySelectorAll('.plus-btn').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-id');
                let jumlahSpan = document.querySelector(`.jumlah-text[data-id="${productId}"]`);
                let jumlahHidden = document.querySelector(`#jumlah-${productId}`);
                let max = parseInt(jumlahHidden.getAttribute('max')); // Mengambil stok dari hidden input
                let currentValue = parseInt(jumlahSpan.innerText) || 1;

                if (currentValue < max) {
                    jumlahSpan.innerText = currentValue + 1;
                    jumlahHidden.value = currentValue + 1;
                    updateBeliJumlah(productId, currentValue + 1);
                }
            });
        });

        function updateBeliJumlah(productId, value) {
            let beliJumlahInput = document.querySelector(`input.beli-jumlah[id="beli-jumlah-${productId}"]`);
            if (beliJumlahInput) {
                beliJumlahInput.value = value;
            }
        }
    });
</script>
<script>
    document.getElementById("filterHarga").addEventListener("change", function () {
        let filter = this.value;
        let produkContainer = document.getElementById("produkContainer");
        let produkItems = Array.from(produkContainer.getElementsByClassName("produk-item"));
        
        if (filter === "harga_lama") {
            produkItems.sort((a, b) => b.dataset.hargaLama - a.dataset.hargaLama);
        } else if (filter === "harga_terendah") {
            produkItems.sort((a, b) => a.dataset.harga - b.dataset.harga);
        }
        
        produkContainer.innerHTML = "";
        produkItems.forEach(item => produkContainer.appendChild(item));
    });
</script>