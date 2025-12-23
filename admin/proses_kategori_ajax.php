<?php
header('Content-Type: application/json');
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $tipe = $_POST['tipe_hitung'];

    // Cek duplikat
    $cek = mysqli_query($conn, "SELECT id FROM categories WHERE nama_kategori = '$nama'");
    if(mysqli_num_rows($cek) > 0){
        echo json_encode(['status' => 'error', 'message' => 'Nama Kategori sudah ada!']);
        exit;
    }

    $query = "INSERT INTO categories (nama_kategori, tipe_hitung) VALUES ('$nama', '$tipe')";
    if(mysqli_query($conn, $query)){
        $id_baru = mysqli_insert_id($conn);
        echo json_encode([
            'status' => 'success', 
            'message' => 'Kategori berhasil ditambahkan',
            'data' => [
                'id' => $id_baru,
                'nama' => $nama,
                'tipe' => $tipe
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal DB: ' . mysqli_error($conn)]);
    }
}
?>