<?php
include 'includes/db.php'; // DB connection hanya jika dibutuhkan di halaman ini
include 'includes/header.php';

$id = $_GET['id'] ?? 0;
$id = intval($id); // sanitize

// Ambil data rumah
$result = mysqli_query($conn, "SELECT * FROM rumah WHERE id = $id");
$data = mysqli_fetch_assoc($result);
if (!$data) {
    echo "<div class='container mt-4'><h3>Data rumah tidak ditemukan.</h3></div>";
    include 'includes/footer.php';
    exit;
}

// Ambil gambar tambahan
$gambar_tambahan_result = mysqli_query($conn, "SELECT * FROM rumah_images WHERE rumah_id = $id");
$gambar_tambahan = [];
if ($gambar_tambahan_result) {
    while ($row = mysqli_fetch_assoc($gambar_tambahan_result)) {
        $gambar_tambahan[] = $row['filename'];
    }
}

// Buat array semua gambar untuk slider dan thumbnail
$all_images = [];
$all_images[] = $data['gambar_utama']; // Gambar utama selalu di awal

if (!empty($gambar_tambahan)) {
    $all_images = array_merge($all_images, $gambar_tambahan);
}

$total_gambar = count($all_images);
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-7">
            <div id="carouselRumahDetail" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded shadow-sm">
                    <?php foreach ($all_images as $index => $image_filename): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="uploads/<?= htmlspecialchars($image_filename) ?>" class="d-block w-100" style="object-fit: cover; max-height: 500px;" alt="Gambar Rumah <?= $index + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_gambar > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselRumahDetail" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselRumahDetail" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($total_gambar > 1): ?>
                <div class="d-flex overflow-auto mt-3" style="white-space: nowrap;">
                    <?php foreach ($all_images as $index => $image_filename): ?>
                        <img src="uploads/<?= htmlspecialchars($image_filename) ?>"
                             class="img-thumbnail me-2 cursor-pointer"
                             style="width: 80px; height: 60px; object-fit: cover; cursor: pointer; border: 2px solid transparent;"
                             alt="Thumbnail <?= $index + 1 ?>"
                             data-bs-target="#carouselRumahDetail"
                             data-bs-slide-to="<?= $index ?>"
                             onclick="this.style.borderColor='#007bff';" // Highlight active thumbnail (opsional)
                             onmouseover="this.style.opacity='0.8';"
                             onmouseout="this.style.opacity='1'; this.style.borderColor='transparent';">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title"><?= htmlspecialchars($data['nama']) ?></h1>
                    <p class="card-text text-muted mb-2">
                        <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($data['lokasi']) ?>
                    </p>
                    <h3 class="text-success mb-4">Rp <?= number_format($data['harga'], 0, ',', '.') ?></h3>

                    <hr>

                    <h5 class="mt-4">Deskripsi</h5>
                    <div class="text-break">
                        <?= nl2br(htmlspecialchars($data['deskripsi'])) ?>
                    </div>

                    <hr>

                    <a href="https://wa.me/62xxxxxxxxxxx?text=Halo%20saya%20tertarik%20dengan%20rumah%20<?= urlencode($data['nama']) ?>" target="_blank" class="btn btn-success btn-lg w-100 mt-3">
                        <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Pastikan skrip ini hanya ada di halaman detail jika spesifik untuk thumbnail
    document.addEventListener('DOMContentLoaded', function() {
        var carouselElement = document.getElementById('carouselRumahDetail');
        var carousel = new bootstrap.Carousel(carouselElement);

        var thumbnails = document.querySelectorAll('.img-thumbnail');

        function updateThumbnailBorder(activeIndex) {
            thumbnails.forEach(function(thumb, idx) {
                if (idx === activeIndex) {
                    thumb.style.borderColor = '#007bff';
                } else {
                    thumb.style.borderColor = 'transparent';
                }
            });
        }

        updateThumbnailBorder(0); // Set border untuk thumbnail pertama saat dimuat

        thumbnails.forEach(function(thumbnail, idx) {
            thumbnail.addEventListener('click', function() {
                carousel.to(idx);
                updateThumbnailBorder(idx);
            });
        });

        carouselElement.addEventListener('slid.bs.carousel', function () {
            var activeIndex = Array.from(document.querySelectorAll('#carouselRumahDetail .carousel-item')).indexOf(document.querySelector('#carouselRumahDetail .carousel-item.active'));
            updateThumbnailBorder(activeIndex);
        });
    });
</script>