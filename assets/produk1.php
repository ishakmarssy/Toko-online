<!-- Produk -->
<div class="container mt-5">
    
    <h3 class="text-left mb-3 "><i class="bi bi-tags"></i> Semua Produk</h3>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php
        include("assets/kon.php");

        $kategori = $_GET['kategori'] ?? 'semua';
        $filter = $_GET['filter'] ?? 'default';

        // Base query
        $where = "WHERE 1";
        if ($kategori != 'semua') {
            $where .= " AND kategori = '$kategori'";
        }

        if ($filter == 'harga_lama') {
            $where .= " AND harga_lama > 0";
            $order = "ORDER BY id DESC";
        } elseif ($filter == 'harga_terendah') {
            $order = "ORDER BY harga ASC";
        } else {
            $order = "ORDER BY id DESC";
        }

        $sql = "SELECT * FROM produk $where $order";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>

        <?php
        include("api/card_produk.php");
        ?>

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