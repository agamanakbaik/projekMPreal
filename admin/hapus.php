<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 1. Hapus file foto fisik dulu agar server tidak penuh
    $q = mysqli_query($conn, "SELECT foto FROM products WHERE id='$id'");
    $data = mysqli_fetch_assoc($q);
    $foto = "../assets/img/" . $data['foto'];
    
    if (file_exists($foto)) {
        unlink($foto); // Perintah hapus file
    }

    // 2. Hapus data di database
    // Karena kita pakai ON DELETE CASCADE di database (Tahap 1), 
    // data di tabel product_variants juga otomatis terhapus.
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    
    header("Location: produk.php");
}
?>