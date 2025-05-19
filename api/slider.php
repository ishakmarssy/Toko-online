<div class="container mt-0 carousel-container">
    <h2 class="text-start"></h2>
    <div id="sliderCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $active = "active";
            include("assets/kon.php");
                $result = $conn->query("SELECT * FROM slider");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="carousel-item ' . $active . '">
                            <a href="' . $row["link"] . '" target="_blank">
                                <img src="uploads/' . $row["gmbr"] . '" class="d-block w-100" alt="Slide">
                            </a>
                          </div>';
                    $active = ""; // Hanya item pertama yang aktif
                }
            } else {
                echo '<div class="carousel-item active">
                        <div class="text-center p-5">No slides available</div>
                      </div>';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
    <hr>
</div>


