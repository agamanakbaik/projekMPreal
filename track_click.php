<?php
include 'config/koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    // Update jumlah klik + 1
    mysqli_query($conn, "UPDATE products SET jumlah_klik = jumlah_klik + 1 WHERE id = '$id'");
}
?>