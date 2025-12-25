<?php
session_start();
include '../config/koneksi.php';
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metode request tidak valid.");
    }

    // 1. Validasi Input
    if (empty($_POST['nama']) || empty($_POST['category_id'])) {
        throw new Exception("Nama Produk dan Kategori wajib diisi!");
    }

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $brand_id = (int)$_POST['brand_id'];
    $category_id = (int)$_POST['category_id'];
    $hpp_dasar = (float)($_POST['hpp_dasar'] ?? 0);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Upload Foto Utama
    $foto_nama = "";
    if(!empty($_FILES['foto']['name'])){
        $foto_nama = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/" . $foto_nama);
    }

    $harga_terendah = 0;
    if(isset($_POST['varian']) && is_array($_POST['varian'])){
        $harga_terendah = (float)$_POST['varian'][0]['harga']; 
    }

    // 2. Simpan Produk
    $query_prod = "INSERT INTO products (nama_produk, brand_id, category_id, hpp_modal, harga_jual, deskripsi, foto) 
                   VALUES ('$nama', '$brand_id', '$category_id', '$hpp_dasar', '$harga_terendah', '$deskripsi', '$foto_nama')";
    
    if (!mysqli_query($conn, $query_prod)) {
        throw new Exception("Gagal simpan produk: " . mysqli_error($conn));
    }

    // DAPATKAN ID PRODUK BARU
    $product_id = mysqli_insert_id($conn);
    $rules_updated = false; 

    // ==========================================================
    // LOGIKA BARU: UPLOAD GALERI TAMBAHAN
    // ==========================================================
    if(isset($_FILES['foto_galeri']) && !empty($_FILES['foto_galeri']['name'][0])) {
        $total_files = count($_FILES['foto_galeri']['name']);
        
        for($i = 0; $i < $total_files; $i++) {
            $nama_file = $_FILES['foto_galeri']['name'][$i];
            $tmp_file  = $_FILES['foto_galeri']['tmp_name'][$i];
            $error     = $_FILES['foto_galeri']['error'][$i];

            if($error === 0) {
                // Buat nama unik agar tidak bentrok (Time + index + nama asli)
                $nama_baru = time() . '_' . $i . '_' . $nama_file;
                $tujuan = '../assets/img/' . $nama_baru;

                if(move_uploaded_file($tmp_file, $tujuan)) {
                    // Simpan ke tabel product_images
                    mysqli_query($conn, "INSERT INTO product_images (product_id, nama_gambar) VALUES ('$product_id', '$nama_baru')");
                }
            }
        }
    }
    // ==========================================================


    // 3. Simpan Varian
    if(isset($_POST['varian']) && is_array($_POST['varian'])){
        
        // A. Hapus Rule Lama (Hanya jika dicentang)
        if(isset($_POST['simpan_aturan'])) {
            mysqli_query($conn, "DELETE FROM pricing_rules WHERE category_id = '$category_id'");
            $rules_updated = true;
        }

        foreach($_POST['varian'] as $v){
            $ukuran = mysqli_real_escape_string($conn, $v['ukuran']);
            $modal = (float)$v['modal'];
            $harga = (float)$v['harga'];
            
            // Simpan Varian Produk
            $q_var = "INSERT INTO product_variants (product_id, ukuran, harga_modal, harga_jual) 
                      VALUES ('$product_id', '$ukuran', '$modal', '$harga')";
            mysqli_query($conn, $q_var);

            // B. Simpan Rule Baru (Hanya jika dicentang)
            if(isset($_POST['simpan_aturan'])) {
                $pengali = (float)($v['pengali'] ?? 0);
                
                if($pengali == 0 && $hpp_dasar > 0) {
                    $pengali = $modal / $hpp_dasar;
                }

                $margin_persen = 0;
                if($modal > 0) {
                    $margin_persen = (($harga - $modal) / $modal) * 100;
                }

                $q_rule = "INSERT INTO pricing_rules (category_id, label_ukuran, pengali, margin_persen, is_custom_karton) 
                           VALUES ('$category_id', '$ukuran', '$pengali', '$margin_persen', 0)";
                mysqli_query($conn, $q_rule);
            }
        }
    }

    // Kirim status spesifik ke Frontend
    echo json_encode(['status' => 'success', 'rules_updated' => $rules_updated]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>