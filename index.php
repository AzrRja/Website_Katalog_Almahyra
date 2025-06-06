<?php
include 'includes/db.php'; // Sertakan file koneksi database
include 'includes/header.php'; // Sertakan header dan navbar
?>

<div id="carouselHero" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="uploads/rumah1.jpg" class="d-block w-100" style="height: 600px; object-fit: cover;" alt="Rumah 1">
            <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                <div class="text-center">
                    <h1 class="display-3 fw-bold text-white mb-3">Temukan Rumah Impian Anda</h1>
                    <p class="lead text-white mb-4">Koleksi properti terbaik di Lampung dengan harga bersaing.</p>
                    <a href="rumah.php" class="btn btn-primary btn-lg px-4 me-md-2" role="button">Lihat Semua Rumah <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="uploads/rumah2.jpg" class="d-block w-100" style="height: 600px; object-fit: cover;" alt="Rumah 2">
            <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                <div class="text-center">
                    <h1 class="display-3 fw-bold text-white mb-3">Investasi Cerdas, Masa Depan Cerah</h1>
                    <p class="lead text-white mb-4">Properti pilihan di lokasi strategis yang terus berkembang.</p>
                    <a href="rumah.php" class="btn btn-outline-light btn-lg px-4" role="button">Jelajahi Sekarang <i class="bi bi-search"></i></a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="uploads/rumah3.jpg" class="d-block w-100" style="height: 600px; object-fit: cover;" alt="Rumah 3">
            <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                <div class="text-center">
                    <h1 class="display-3 fw-bold text-white mb-3">Pelayanan Prima, Proses Mudah</h1>
                    <p class="lead text-white mb-4">Kami siap membantu Anda menemukan properti terbaik.</p>
                    <a href="https://wa.me/6281234567890?text=Halo%20saya%20tertarik%20dengan%20properti%20di%20Almahyra%20Properti" target="_blank" class="btn btn-success btn-lg px-4" role="button">Hubungi Kami <i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Mengapa Memilih Almahyra Properti?</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-geo-alt-fill text-primary display-4 mb-3"></i>
                        <h5 class="card-title fw-bold">Lokasi Strategis</h5>
                        <p class="card-text">Properti di lokasi premium dengan akses mudah ke fasilitas umum.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-currency-dollar text-success display-4 mb-3"></i>
                        <h5 class="card-title fw-bold">Harga Kompetitif</h5>
                        <p class="card-text">Penawaran terbaik dengan harga yang bersaing di pasar properti.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-award-fill text-warning display-4 mb-3"></i>
                        <h5 class="card-title fw-bold">Legalitas Aman</h5>
                        <p class="card-text">Semua properti memiliki legalitas yang terjamin dan aman.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Ambil 3 rumah terbaru dari database
$query_latest_rumah = mysqli_query($conn, "SELECT id, nama, lokasi, harga, gambar_utama FROM rumah ORDER BY id DESC LIMIT 3");
$has_latest_rumah = mysqli_num_rows($query_latest_rumah) > 0;
?>

<?php if ($has_latest_rumah): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Properti Terbaru Kami</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($rumah = mysqli_fetch_assoc($query_latest_rumah)): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="uploads/<?= htmlspecialchars($rumah['gambar_utama']) ?>" class="card-img-top" alt="<?= htmlspecialchars($rumah['nama']) ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($rumah['nama']) ?></h5>
                        <p class="card-text text-muted"><i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($rumah['lokasi']) ?></p>
                        <p class="card-text fs-5 text-success"><strong>Rp <?= number_format($rumah['harga'], 0, ',', '.') ?></strong></p>
                        <a href="detail.php?id=<?= $rumah['id'] ?>" class="btn btn-primary w-100 mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-5">
            <a href="rumah.php" class="btn btn-outline-primary btn-lg">Lihat Semua Properti <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="about text-center py-5 bg-light" id="tentang">
    <div class="container">
        <h2 class="mb-4 fw-bold">Tentang Almahyra Properti</h2>
        <p class="lead">Almahyra Properti adalah penyedia layanan jual beli rumah terbaik di Lampung. Kami menawarkan rumah berkualitas dengan lokasi strategis, harga kompetitif, dan legalitas aman.</p>
        <p>Dengan pengalaman bertahun-tahun di industri properti, kami berkomitmen untuk memberikan solusi terbaik bagi kebutuhan hunian dan investasi Anda. Kepuasan klien adalah prioritas utama kami.</p>
        <a href="rumah.php" class="btn btn-primary btn-lg mt-4">Lihat Daftar Rumah</a>
    </div>
</section>

<div class="hidden-admin-login" id="adminLoginButton">
    <a href="admin/login.php" class="btn btn-dark btn-sm shadow-lg">Login Admin</a>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // JavaScript untuk Admin Login Tersembunyi
    document.addEventListener('DOMContentLoaded', function() {
        const adminTrigger = document.getElementById('adminTrigger');
        const adminLoginButton = document.getElementById('adminLoginButton');
        let clickCount = 0;
        const requiredClicks = 5; // Jumlah klik untuk menampilkan tombol

        if (adminTrigger && adminLoginButton) {
            adminTrigger.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah link dari navigasi
                clickCount++;
                if (clickCount >= requiredClicks) {
                    adminLoginButton.style.display = 'block'; // Tampilkan tombol
                    clickCount = 0; // Reset hitungan setelah ditampilkan
                }
                // Opsional: reset hitungan setelah beberapa waktu jika tidak ada klik lagi
                clearTimeout(adminTrigger.timeoutId);
                adminTrigger.timeoutId = setTimeout(() => {
                    clickCount = 0;
                }, 1500); // Reset jika tidak ada klik dalam 1.5 detik
            });
        }

        // JavaScript untuk memastikan carousel caption tengah secara vertikal
        var carouselHero = document.getElementById('carouselHero');
        if (carouselHero) {
            var captions = carouselHero.querySelectorAll('.carousel-caption');
            captions.forEach(function(caption) {
                caption.style.position = 'absolute'; // Pastikan absolute untuk h-100
                caption.style.top = '0';
                caption.style.left = '0';
                caption.style.right = '0';
                caption.style.bottom = '0';
            });
        }
    });
</script>