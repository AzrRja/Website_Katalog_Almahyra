<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';

$id = $_GET['id'];

// Hapus gambar terlebih dahulu (opsional tapi disarankan)
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rumah WHERE id = $id"));
$gambar = $data['gambar_utama'];
if (file_exists("../uploads/$gambar")) {
    unlink("../uploads/$gambar");
}

// Hapus data rumah dari database
mysqli_query($conn, "DELETE FROM rumah WHERE id = $id");

header("Location: dashboard.php");
exit;
