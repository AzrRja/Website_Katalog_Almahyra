<?php
session_start();
include '../includes/db.php'; // Pastikan path ini benar relatif terhadap delete_additional_image.php

// Cek apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum
    exit;
}

$image_id = $_GET['image_id'] ?? 0;
$rumah_id = $_GET['rumah_id'] ?? 0;

// Sanitize input
$image_id = intval($image_id);
$rumah_id = intval($rumah_id);

if ($image_id > 0 && $rumah_id > 0) {
    // 1. Ambil nama file gambar dari database
    $query_get_filename = mysqli_query($conn, "SELECT filename FROM rumah_images WHERE id = $image_id AND rumah_id = $rumah_id");

    if ($query_get_filename && mysqli_num_rows($query_get_filename) > 0) {
        $image_data = mysqli_fetch_assoc($query_get_filename);
        $filename = $image_data['filename'];
        $upload_dir = '../uploads/'; // Sesuaikan path ke folder uploads

        // 2. Hapus entry dari database
        $query_delete_db = mysqli_query($conn, "DELETE FROM rumah_images WHERE id = $image_id AND rumah_id = $rumah_id"); // Tambah rumah_id untuk keamanan
        if ($query_delete_db) {
            // 3. Hapus file fisik dari server
            if (file_exists($upload_dir . $filename)) {
                unlink($upload_dir . $filename);
            }
            $_SESSION['message'] = "<div class='alert alert-success'>Gambar tambahan berhasil dihapus!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>Gagal menghapus gambar dari database: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Gambar tidak ditemukan atau tidak terkait dengan rumah ini.</div>";
    }
} else {
    $_SESSION['message'] = "<div class='alert alert-warning'>ID gambar atau rumah tidak valid.</div>";
}

// Redirect kembali ke halaman edit rumah setelah operasi
header("Location: edit.php?id=" . $rumah_id);
exit;
?>