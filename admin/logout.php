<?php
session_start();

// 1. Kosongkan semua variabel session
$_SESSION = [];

// 2. Hapus session dari memori server
session_unset();

// 3. Hancurkan session ID
session_destroy();

// 4. Lempar kembali ke halaman depan (Publik)
// CATATAN: Sesuaikan path ini. 
// Jika logout.php ada di dalam folder 'admin', gunakan '../index.php'
// Jika logout.php ada di folder utama, gunakan 'index.php'
header("Location: ../login"); 
exit;
?>