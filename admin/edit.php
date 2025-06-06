<?php
session_start();
include '../includes/db.php'; // Sesuaikan path jika db.php tidak di root includes/

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum
    exit;
}

$id = $_GET['id'] ?? 0;
$id = intval($id); // Sanitize ID

// Ambil data rumah yang akan diedit
$data = null;
if ($id > 0) {
    $result = mysqli_query($conn, "SELECT * FROM rumah WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
    if (!$data) {
        // Jika data tidak ditemukan, redirect atau tampilkan pesan error
        header("Location: dashboard.php");
        exit("Data rumah tidak ditemukan.");
    }
} else {
    header("Location: dashboard.php"); // Redirect jika tidak ada ID
    exit("ID rumah tidak diberikan.");
}

$message = ""; // Untuk pesan sukses/error setelah update

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $tipe = $_POST['tipe'];
    $deskripsi = $_POST['deskripsi'];

    // Sanitize input
    $nama = mysqli_real_escape_string($conn, $nama);
    $lokasi = mysqli_real_escape_string($conn, $lokasi);
    $harga = intval($harga);
    $tipe = mysqli_real_escape_string($conn, $tipe);
    $deskripsi = mysqli_real_escape_string($conn, $deskripsi);

    $sql = "UPDATE rumah SET 
                nama = '$nama', 
                lokasi = '$lokasi', 
                harga = $harga, 
                tipe = '$tipe', 
                deskripsi = '$deskripsi' 
            WHERE id = $id";

    // Handle Gambar Utama (jika diupload)
    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
        $gambar_utama_tmp_name = $_FILES['gambar_utama']['tmp_name'];
        $gambar_utama_name = uniqid() . '-' . basename($_FILES['gambar_utama']['name']); // Nama unik
        $upload_dir = '../uploads/'; // Sesuaikan path ke folder uploads

        // Hapus gambar lama jika ada
        if ($data['gambar_utama'] && file_exists($upload_dir . $data['gambar_utama'])) {
            unlink($upload_dir . $data['gambar_utama']);
        }

        if (move_uploaded_file($gambar_utama_tmp_name, $upload_dir . $gambar_utama_name)) {
            $sql_gambar_utama = "UPDATE rumah SET gambar_utama = '$gambar_utama_name' WHERE id = $id";
            mysqli_query($conn, $sql_gambar_utama);
        } else {
            $message .= "<div class='alert alert-danger'>Gagal mengupload gambar utama.</div>";
        }
    }

    // Handle Gambar Tambahan (jika diupload)
    if (isset($_FILES['gambar_tambahan']) && is_array($_FILES['gambar_tambahan']['name'])) {
        foreach ($_FILES['gambar_tambahan']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gambar_tambahan']['error'][$key] == 0) {
                $gambar_tambahan_name = uniqid() . '-' . basename($_FILES['gambar_tambahan']['name'][$key]);
                if (move_uploaded_file($tmp_name, $upload_dir . $gambar_tambahan_name)) {
                    // Simpan nama file gambar tambahan ke tabel rumah_images
                    $sql_insert_image = "INSERT INTO rumah_images (rumah_id, filename) VALUES ($id, '$gambar_tambahan_name')";
                    mysqli_query($conn, $sql_insert_image);
                } else {
                    $message .= "<div class='alert alert-warning'>Gagal mengupload salah satu gambar tambahan.</div>";
                }
            }
        }
    }


    if (mysqli_query($conn, $sql)) {
        // Ambil kembali data terbaru setelah update
        $result = mysqli_query($conn, "SELECT * FROM rumah WHERE id = $id");
        $data = mysqli_fetch_assoc($result);
        $message .= "<div class='alert alert-success'>Data rumah berhasil diperbarui!</div>";
    } else {
        $message .= "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Ambil gambar tambahan saat ini (untuk ditampilkan)
$gambar_tambahan_result = mysqli_query($conn, "SELECT id, filename FROM rumah_images WHERE rumah_id = $id");
$current_additional_images = [];
if ($gambar_tambahan_result) {
    while ($row = mysqli_fetch_assoc($gambar_tambahan_result)) {
        $current_additional_images[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Rumah - Dashboard Admin</title>
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
        .current-image-preview {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .additional-image-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
        .additional-image-item img {
            max-width: 100px;
            height: auto;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 3px;
            display: block;
            margin-bottom: 5px;
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
            <a href="tambah.php"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Rumah Baru</a>
        </li>
        <li>
            <a href="dashboard.php" class="active"><i class="bi bi-list-columns-reverse me-2"></i> Daftar Rumah</a>
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
        <h2 class="mb-4">Edit Data Rumah</h2>

        <?php echo $message; // Menampilkan pesan sukses/error ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header card-header-custom">
                Form Edit Rumah
            </div>
            <div class="card-body">
                <form action="edit.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Rumah</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= htmlspecialchars($data['lokasi'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="<?= htmlspecialchars($data['harga'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <input type="text" class="form-control" id="tipe" name="tipe" value="<?= htmlspecialchars($data['tipe'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="gambar_utama" class="form-label">Gambar Utama (Opsional, tinggalkan kosong jika tidak berubah)</label>
                        <?php if ($data['gambar_utama']): ?>
                            <img src="../uploads/<?= htmlspecialchars($data['gambar_utama']) ?>" alt="Gambar Utama Saat Ini" class="current-image-preview">
                            <p class="text-muted">File saat ini: <?= htmlspecialchars($data['gambar_utama']) ?></p>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar_utama" name="gambar_utama" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="gambar_tambahan" class="form-label">Gambar Tambahan (Opsional, bisa pilih lebih dari satu)</label>
                        <?php if (!empty($current_additional_images)): ?>
                            <div class="mb-2">
                                <p class="text-muted">Gambar Tambahan Saat Ini:</p>
                                <?php foreach ($current_additional_images as $image): ?>
                                    <div class="additional-image-item">
                                        <img src="../uploads/<?= htmlspecialchars($image['filename']) ?>" alt="Gambar Tambahan" class="img-thumbnail">
                                        <a href="delete_additional_image.php?image_id=<?= $image['id'] ?>&rumah_id=<?= $id ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Yakin ingin menghapus gambar ini?');">
                                            <i class="bi bi-x-circle-fill"></i> Hapus
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar_tambahan" name="gambar_tambahan[]" multiple accept="image/*">
                        <div class="form-text">Anda bisa menambahkan lebih banyak gambar tambahan di sini. Gambar yang sudah ada di atas akan tetap ada kecuali Anda menghapusnya secara manual.</div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-clockwise me-1"></i> Update</button>
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