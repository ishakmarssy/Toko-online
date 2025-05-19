<div id="slider" class="py-4">
    <div class="container mt-3">
        <div class="card shadow-sm p-3">
            <h2 class="mb-3 text-center">Kelola Slider</h2>
            <?php if (isset($_SESSION['message'])): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
  </div>
<?php endif; ?>

            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahSliderModal">
  Tambah Slider
</button>

        <!-- Modal Tambah Slider -->
<div class="modal fade" id="tambahSliderModal" tabindex="-1" aria-labelledby="tambahSliderLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="../api/proses_slider.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahSliderLabel">Tambah Slider Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Gambar</label>
              <input type="file" name="gmbr" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Link</label>
              <input type="text" name="link" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="tambah" class="btn btn-success w-100">Simpan Slider</button>
        </div>
      </form>
    </div>
  </div>
</div>


        
         <!-- Tabel Slider -->
         <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Link</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include("../assets/kon.php");
                        $result = $conn->query("SELECT * FROM slider");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='../uploads/" . basename($row["gmbr"]) . "' class='img-fluid' width='80'></td>";
                            echo "<td>" . $row["link"] . "</td>";
                            echo "<td>
                                    <button class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#editModal_".$row["id"]."'>Edit</button>
                                    <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteModal_".$row["id"]."'>Hapus</button>
                                  </td>";
                            echo "</tr>";
                            
                            // Modal Edit
                            echo "<div class='modal fade' id='editModal_".$row["id"]."' tabindex='-1'>
                                    <div class='modal-dialog modal-sm'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>Edit Slider</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <form action='../api/proses_slider.php' method='POST' enctype='multipart/form-data'>
                                                <div class='modal-body'>
                                                    <input type='hidden' name='id' value='".$row["id"]."'>
                                                    <div class='mb-3'>
                                                        <label class='form-label'>Gambar</label>
                                                        <input type='file' name='gmbr' class='form-control'>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label class='form-label'>Link</label>
                                                        <input type='text' name='link' class='form-control' value='".$row["link"]."' required>
                                                    </div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='submit' name='update' class='btn btn-primary w-100'>Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                  </div>";
                            
                            // Modal Hapus
                            echo "<div class='modal fade' id='deleteModal_".$row["id"]."' tabindex='-1'>
                                    <div class='modal-dialog modal-sm'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>Hapus Slider</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <div class='modal-body text-center'>
                                                Apakah Anda yakin ingin menghapus slider ini?
                                            </div>
                                            <div class='modal-footer'>
                                                <form action='../api/proses_slider.php' method='POST'>
                                                    <input type='hidden' name='id' value='".$row["id"]."'>
                                                    <button type='submit' name='delete' class='btn btn-danger w-100'>Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                  </div>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
