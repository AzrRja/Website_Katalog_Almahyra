<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almahyra Properti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS Umum */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden; /* Mencegah scroll horizontal yang tidak diinginkan */
        }

        /* Navbar Custom Styles */
        .navbar-custom {
            padding-top: 1rem; /* Tambah padding atas */
            padding-bottom: 1rem; /* Tambah padding bawah */
            background-color: #ffffff; /* Pastikan background putih */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* Bayangan lebih halus dan terlihat */
        }
        .navbar-custom .navbar-brand {
            font-size: 1.8rem; /* Ukuran font lebih besar */
            font-weight: 700; /* Lebih tebal */
            color: #0d6efd; /* Warna biru primary Bootstrap */
            transition: color 0.3s ease;
        }
        .navbar-custom .navbar-brand:hover {
            color: #0a58ca; /* Sedikit lebih gelap saat hover */
        }
        .navbar-custom .nav-link {
            font-size: 1.05rem; /* Ukuran font sedikit lebih besar */
            font-weight: 500;
            color: #343a40; /* Dark gray for links */
            padding: 0.5rem 1rem; /* Padding lebih teratur */
            border-radius: 0.375rem; /* Sudut membulat */
            transition: all 0.3s ease;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active { /* Pastikan ini */
            color: #fff; /* Teks putih saat hover/aktif */
            background-color: #0d6efd; /* Background biru primary saat hover/aktif */
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3); /* Bayangan saat hover/aktif */
        }
        .navbar-custom .navbar-toggler {
            border: none; /* Hilangkan border toggler */
        }
        .navbar-custom .navbar-toggler:focus {
            box-shadow: none; /* Hilangkan box-shadow fokus default Bootstrap */
        }


        /* Hero Section (untuk index.php) */
        .hero {
            background: url('uploads/rumah1.jpg') no-repeat center center; /* Pastikan path gambar ini benar */
            background-size: cover;
            height: 400px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
            text-align: center;
            padding: 20px;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.2rem;
        }

        /* About Section (untuk index.php) */
        .about {
            background-color: #f9f9f9;
            padding: 60px 20px;
        }

        /* WhatsApp Floating Button */
        .whatsapp-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .whatsapp-popup img {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .whatsapp-popup img:hover {
            transform: scale(1.1);
        }

        /* Admin Login Button (Hidden by default, untuk index.php) */
        .hidden-admin-login {
            display: none;
            position: fixed;
            bottom: 90px;
            right: 20px;
            z-index: 999;
        }
        .hidden-admin-login .btn {
            font-size: 0.85rem;
            padding: 8px 15px;
        }

        /* Admin Panel Sidebar & Content Styles (untuk halaman admin/) */
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
            display: flex;
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
            flex-grow: 1;
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
        /* Untuk preview gambar di edit.php */
        .current-image-preview {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: block; /* Agar tidak inline dengan teks */
        }
        .additional-image-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            text-align: center;
            vertical-align: top; /* Untuk alignment yang lebih baik */
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

<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php" id="adminTrigger">Almahyra Properti</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" aria-current="page" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'rumah.php') ? 'active' : '' ?>" href="rumah.php">Daftar Rumah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '#tentang') !== false) ? 'active' : '' ?>" href="index.php#tentang">Tentang Kami</a>
                </li>
            </ul>
        </div>
    </div>
</nav>