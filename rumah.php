<?php
include 'includes/db.php'; // Jika halaman ini menampilkan data dari DB
include 'includes/header.php';
?>

<div class="container mt-4">
    <h2>Daftar Rumah Kami</h2>
    <div class="row">
        <?php
        // Contoh loop untuk menampilkan daftar rumah
        $query_rumah = mysqli_query($conn, "SELECT * FROM rumah ORDER BY id DESC");
        if (mysqli_num_rows($query_rumah) > 0) {
            while ($rumah = mysqli_fetch_assoc($query_rumah)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <img src="uploads/<?= htmlspecialchars($rumah['gambar_utama']) ?>" class="card-img-top" alt="<?= htmlspecialchars($rumah['nama']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($rumah['nama']) ?></h5>
                            <p class="card-text text-muted"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($rumah['lokasi']) ?></p>
                            <p class="card-text"><strong>Rp <?= number_format($rumah['harga'], 0, ',', '.') ?></strong></p>
                            <a href="detail.php?id=<?= htmlspecialchars($rumah['id']) ?>" class="btn btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>Belum ada data rumah.</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>