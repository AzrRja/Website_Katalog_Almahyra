<?php
session_start();
include '../includes/db.php'; // Pastikan path ini benar (naik satu level dari folder admin/)

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum
    exit;
}

// ==========================================
// Peningkatan: Menampilkan Notifikasi/Pesan Sistem dari Session
// ==========================================
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}

// ==========================================
// Peningkatan: Statistik Dashboard
// ==========================================
// Total Rumah
$total_rumah_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM rumah");
$total_rumah_data = mysqli_fetch_assoc($total_rumah_query);
$total_rumah = $total_rumah_data['total'];

// Total Nilai Properti (SUM dari kolom harga)
$total_harga_query = mysqli_query($conn, "SELECT SUM(harga) AS total_harga FROM rumah");
$total_harga_data = mysqli_fetch_assoc($total_harga_query);
$total_harga = $total_harga_data['total_harga'];
if ($total_harga === null) { // Jika tidak ada rumah, SUM bisa null
    $total_harga = 0;
}
// ==========================================


// ==========================================
// Peningkatan: Fitur Pencarian pada Tabel
// ==========================================
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

$sql = "SELECT id, nama, lokasi, harga, deskripsi FROM rumah";
if (!empty($search_query)) {
    // Menambahkan kondisi WHERE untuk pencarian
    $sql .= " WHERE nama LIKE '%$search_query%' OR lokasi LIKE '%$search_query%' OR deskripsi LIKE '%$search_query%'";
}
$sql .= " ORDER BY id DESC"; // Urutkan berdasarkan ID terbaru

$rumah_query = mysqli_query($conn, $sql);
// ==========================================
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Almahyra Properti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS Admin Panel Sidebar & Content Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden; /* Mencegah scroll horizontal yang tidak diinginkan */
        }
        #sidebar {
            width: 250px;
            height: 100vh; /* Tinggi penuh viewport */
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40; /* Dark background */
            color: white;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
            display: flex; /* Tambahkan flexbox untuk sticky footer */
            flex-direction: column;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        #sidebar ul.components {
            padding: 20px 0;
            list-style: none;
            margin: 0;
            flex-grow: 1; /* Komponen menu akan mengisi sisa ruang */
        }
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: #dee2e6;
            text-decoration: none;
            transition: all 0.3s;
        }
        #sidebar ul li a:hover, #sidebar ul li a.active {
            color: #fff;
            background: #495057;
            text-decoration: none;
        }
        #content {
            margin-left: 250px; /* Offset content by sidebar width */
            padding: 20px;
            transition: all 0.3s;
        }
        .navbar-top {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            margin-left: 250px;
            transition: all 0.3s;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px; /* Hide sidebar by default on small screens */
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content, .navbar-top {
                margin-left: 0;
            }
            #sidebarCollapse {
                position: absolute;
                top: 15px;
                left: 15px;
                z-index: 1001;
            }
            .navbar-top .container-fluid {
                justify-content: flex-end;
            }
            .navbar-top {
                padding-left: 60px;
            }
        }
        .card-header-custom {
            background-color: #0d6efd;
            color: white;
        }
        .table img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }
        /* Style untuk statistik cards di dashboard */
        .statistic-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .statistic-card .icon {
            font-size: 2.5rem;
            color: #0d6efd;
        }
        .statistic-card .value {
            font-size: 2.2rem;
            font-weight: bold;
            margin-top: 10px;
            color: #343a40;
        }
        .statistic-card .label {
            font-size: 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-top">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-info d-block d-md-none">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1 ms-auto me-3">Selamat Datang, Admin!</span>
        <a href="logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<nav id="sidebar">
    <div class="sidebar-header">
        <h3><i class="bi bi-speedometer2 me-2"></i> Admin Panel</h3>
        <p class="text-muted small">Halo, <?= htmlspecialchars($_SESSION['admin']) ?></p>
    </div>

    <ul class="list-unstyled components">
        <li>
            <a href="dashboard.php" class="active"><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
        </li>
        <li>
            <a href="tambah.php"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Rumah Baru</a>
        </li>
        <li>
            <a href="dashboard.php"><i class="bi bi-list-columns-reverse me-2"></i> Daftar Rumah</a>
        </li>
        </ul>
    <ul class="list-unstyled components logout-btn mt-auto">
        <li>
            <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </li>
    </ul>
</nav>

<div id="content">
    <div class="container-fluid">
        <h2 class="mb-4">Dashboard Overview</h2>

        <?php echo $message; // Pesan dari session akan muncul di sini ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="statistic-card">
                    <div class="icon"><i class="bi bi-house-fill"></i></div>
                    <div class="value"><?= $total_rumah ?></div>
                    <div class="label">Total Rumah</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="statistic-card">
                    <div class="icon"><i class="bi bi-currency-dollar"></i></div>
                    <div class="value">Rp <?= number_format($total_harga, 0, ',', '.') ?></div>
                    <div class="label">Total Nilai Properti</div>
                </div>
            </div>
            </div>

        <h2 class="mb-4">Daftar Properti</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                Data Rumah
                <a href="tambah.php" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Rumah
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <form method="GET" class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Cari berdasarkan nama, lokasi, atau deskripsi..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                        <?php if (!empty($search_query)): ?>
                            <a href="dashboard.php" class="btn btn-outline-secondary ms-2"><i class="bi bi-x-lg"></i> Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if (mysqli_num_rows($rumah_query) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Deskripsi Singkat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($data = mysqli_fetch_assoc($rumah_query)): ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= htmlspecialchars($data['nama']) ?></td>
                                <td><?= htmlspecialchars($data['lokasi']) ?></td>
                                <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars(substr($data['deskripsi'], 0, 100)) ?><?= (strlen($data['deskripsi']) > 100) ? '...' : '' ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $data['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus data ini?');">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        <?php if (!empty($search_query)): ?>
                            Tidak ditemukan data rumah yang sesuai dengan pencarian "<?= htmlspecialchars($search_query) ?>".
                        <?php else: ?>
                            Belum ada data rumah yang ditambahkan.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    // JavaScript untuk toggle sidebar di layar kecil
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const sidebarCollapse = document.getElementById('sidebarCollapse');

        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        }
    });
</script>
</body>
</html>