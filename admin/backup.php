<?php
session_start();
include '../config/koneksi.php';

// CEK KEAMANAN (Hanya Super Admin)
if (!isset($_SESSION['status_login']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: index"); exit;
}

// --- LOGIKA BACKUP (Hanya jalan jika ada parameter ?proses=1) ---
if (isset($_GET['proses'])) {
    
    // Fungsi Backup
    function backupDatabase($host, $user, $pass, $name) {
        $mysqli = new mysqli($host, $user, $pass, $name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables = $mysqli->query('SHOW TABLES');
        $content = "-- Backup Database: $name \n-- Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

        while($row = $queryTables->fetch_row()) {
            $table = $row[0];
            // Structure
            $content .= "-- Tabel: $table \n";
            $row2 = $mysqli->query('SHOW CREATE TABLE '.$table)->fetch_row();
            $content .= $row2[1].";\n\n";

            // Data
            $result = $mysqli->query('SELECT * FROM '.$table);
            while($row = $result->fetch_row()) {
                $content .= "INSERT INTO $table VALUES(";
                $vals = array();
                foreach($row as $r) {
                    $r = addslashes($r);
                    $r = str_replace("\n","\\n",$r);
                    $vals[] = '"'.$r.'"';
                }
                $content .= implode(',', $vals);
                $content .= ");\n";
            }
            $content .= "\n";
        }

        // Download File
        $filename = 'backup_db_marhaban_' . date('Ymd_His') . '.sql';
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$filename."\""); 
        echo $content; exit;
    }

    // Eksekusi
    backupDatabase($host, $user, $pass, $db);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Backup Database</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#09AFB5', primaryHover: '#078d91' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Backup Database</h1>
                <p class="text-gray-500 text-sm mt-1">Amankan data sistem secara berkala.</p>
            </div>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg border-t-4 border-primary overflow-hidden">
                <div class="p-8 text-center">
                    
                    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-database text-4xl text-primary"></i>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Backup Data Sekarang</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">
                        Sistem akan mengunduh file <b>.sql</b> berisi seluruh data produk, transaksi, dan user. 
                        Simpan file ini di tempat yang aman.
                    </p>

                    <button onclick="konfirmasiBackup()" class="bg-primary hover:bg-primaryHover text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center gap-2 mx-auto">
                        <i class="fas fa-download"></i> Download Backup Database
                    </button>

                </div>
                <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i> Terakhir diakses: <?= date('d M Y H:i') ?>
                    </p>
                </div>
            </div>
        </div>

    </main>

    <script>
        function konfirmasiBackup() {
            Swal.fire({
                title: 'Siap Backup?',
                text: "File database (.sql) akan diunduh ke komputer Anda.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#09AFB5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Download!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 1. Tampilkan Toast "Memproses"
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Backup dimulai! Cek folder download Anda.'
                    });

                    // 2. Redirect ke Logic PHP untuk memicu download
                    // Beri jeda sedikit agar animasi toast terlihat smooth
                    setTimeout(() => {
                        window.location.href = "?proses=1";
                    }, 1000);
                }
            })
        }
    </script>

</body>
</html>