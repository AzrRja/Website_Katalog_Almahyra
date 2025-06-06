<?php
session_start();
include '../includes/db.php'; // Pastikan path ini benar relative to tambah.php

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum
    exit;
}

$message = ""; // Untuk menampilkan pesan sukses/error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga = intval($_POST['harga']); // Pastikan harga adalah integer
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $upload_dir = '../uploads/'; // Folder tempat menyimpan gambar, sesuaikan path ini!

    // Inisialisasi variabel gambar utama
    $gambar_utama_filename = null;
    $uploaded_additional_images = []; // Untuk menyimpan nama file gambar tambahan

    // Validasi dan Proses Upload Gambar
    if (isset($_FILES['gambar']) && is_array($_FILES['gambar']['name']) && count($_FILES['gambar']['name']) > 0) {
        $files = $_FILES['gambar'];
        $jumlah_gambar = count($files['name']);

        // Pastikan minimal ada 1 gambar diupload
        if ($files['error'][0] != UPLOAD_ERR_NO_FILE) { // Memastikan ada file diupload (bukan kosong)
            for ($i = 0; $i < $jumlah_gambar; $i++) {
                if ($files['error'][$i] == UPLOAD_ERR_OK) {
                    $original_filename = basename($files['name'][$i]);
                    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '.' . $extension; // Buat nama file unik

                    $tmpPath = $files['tmp_name'][$i];
                    $targetPath = $upload_dir . $unique_filename;

                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        if ($gambar_utama_filename === null) {
                            $gambar_utama_filename = $unique_filename; // Gambar pertama jadi gambar utama
                        } else {
                            $uploaded_additional_images[] = $unique_filename; // Sisanya jadi gambar tambahan
                        }
                    } else {
                        $message .= "<div class='alert alert-danger'>Gagal mengupload file: " . htmlspecialchars($original_filename) . ".</div>";
                    }
                } else {
                    $message .= "<div class='alert alert-warning'>Error upload file " . htmlspecialchars($files['name'][$i]) . ": " . $files['error'][$i] . "</div>";
                }
            }
        } else {
            $message = "<div class='alert alert-danger'>Minimal satu gambar harus diupload.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Minimal satu gambar harus diupload.</div>";
    }

    // Jika tidak ada error upload dan gambar utama sudah diatur
    if (empty($message) && $gambar_utama_filename !== null) {
        // Simpan data ke tabel rumah
        $query_rumah = "INSERT INTO rumah (nama, lokasi, harga, tipe, deskripsi, gambar_utama) 
                        VALUES ('$nama', '$lokasi', $harga, '$tipe', '$deskripsi', '$gambar_utama_filename')";
        
        if (mysqli_query($conn, $query_rumah)) {
            $rumah_id = mysqli_insert_id($conn); // Ambil ID rumah yang baru ditambahkan

            // Simpan gambar tambahan ke tabel rumah_images
            foreach ($uploaded_additional_images as $filename) {
                // Perhatikan: Mengubah nama kolom dari 'gambar' menjadi 'filename' agar konsisten
                // dengan edit.php dan skema database yang lebih baik
                $sql_insert_image = "INSERT INTO rumah_images (rumah_id, filename) VALUES ($rumah_id, '$filename')";
                mysqli_query($conn, $sql_insert_image);
            }
            $message = "<div class='alert alert-success'>Data rumah dan gambar berhasil ditambahkan!</div>";
            // Redirect setelah sukses (opsional, bisa juga tampilkan pesan sukses lalu biarkan di halaman ini)
            header("Location: dashboard.php");
            exit;

        } else {
            $message = "<div class='alert alert-danger'>Gagal menyimpan data rumah: " . mysqli_error($conn) . "</div>";
        }
    } else if (empty($message)) { // Jika tidak ada pesan error tapi gambar utama null
        $message = "<div class='alert alert-danger'>Terjadi kesalahan tak terduga saat upload gambar.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Rumah - Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        #sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
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
            margin-left: 250px;
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
                margin-left: -250px;
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
            <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
        </li>
        <li>
            <a href="tambah.php" class="active"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Rumah Baru</a>
        </li>
        <li>
            <a href="dashboard.php"><i class="bi bi-list-columns-reverse me-2"></i> Daftar Rumah</a>
        </li>
        </ul>
    <ul class="list-unstyled components logout-btn" style="margin-top: auto;">
        <li>
            <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </li>
    </ul>
</nav>

<div id="content">
    <div class="container-fluid">
        <h2 class="mb-4">Tambah Data Rumah</h2>

        <?php echo $message; // Menampilkan pesan sukses/error ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-custom">
                Form Tambah Rumah
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Rumah</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" name="harga" id="harga" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <input type="text" name="tipe" id="tipe" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Upload Gambar (boleh lebih dari 1)</label>
                        <input type="file" name="gambar[]" id="gambar" class="form-control" accept="image/*" multiple required>
                        <div class="form-text">Gambar pertama akan menjadi gambar utama.</div>
                    </div>
                    <button class="btn btn-success" type="submit"><i class="bi bi-plus-lg me-1"></i> Simpan</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                </form>
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