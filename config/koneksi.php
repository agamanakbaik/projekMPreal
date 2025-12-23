<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_marhaban";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- AMBIL DATA INFO TOKO GLOBAL ---
// Kita hanya mengambil dari tabel 'info_toko' agar sinkron dengan Admin Panel
$q_info_toko = mysqli_query($conn, "SELECT * FROM info_toko LIMIT 1");

if(mysqli_num_rows($q_info_toko) > 0){
    $info_toko = mysqli_fetch_assoc($q_info_toko);
} else {
    // Data default jika database kosong/error
    $info_toko = [
        'nama_toko' => 'Marhaban Parfume',
        'no_wa' => '628123456789',
        'alamat' => '-',
        'deskripsi' => '-',
        'logo' => '',
        'link_fb' => '',
        'link_ig' => '',
        'link_tiktok' => '',
        'link_maps' => ''
    ];
}
?>