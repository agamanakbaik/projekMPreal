<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login");
    exit;
}

// --- LOGIKA PROSES DENGAN SESSION NOTIFIKASI ---

// 1. Upload Banner
if (isset($_POST['upload'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $foto_baru = time() . "_" . $foto;
    
    if(empty($foto)) {
        $_SESSION['notif'] = ['type' => 'warning', 'text' => 'Harap pilih file gambar!'];
    } else {
        $ekstensi = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $valid = ['jpg', 'jpeg', 'png', 'webp'];
        
        if(in_array($ekstensi, $valid)){
            if (move_uploaded_file($tmp, "../assets/img/" . $foto_baru)) {
                mysqli_query($conn, "INSERT INTO banners (judul, gambar) VALUES ('$judul', '$foto_baru')");
                $_SESSION['notif'] = ['type' => 'success', 'text' => 'Banner Berhasil Diupload!'];
            } else {
                $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal mengupload file.'];
            }
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Format harus JPG, PNG, atau WEBP!'];
        }
    }
    header("Location: banner");
    exit;
}

// 2. Hapus Banner
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($conn, "SELECT gambar FROM banners WHERE id='$id'");
    $row = mysqli_fetch_assoc($q);
    
    // Hapus file fisik
    if($row['gambar'] != '' && file_exists("../assets/img/" . $row['gambar'])){
        unlink("../assets/img/" . $row['gambar']); 
    }
    
    $delete = mysqli_query($conn, "DELETE FROM banners WHERE id='$id'");
    
    if($delete) {
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Banner berhasil dihapus!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus data.'];
    }
    header("Location: banner");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Banner Promo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#09AFB5',
                        primaryHover: '#078d91',
                    }
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
                <h1 class="text-3xl font-bold text-gray-800">Kelola Banner Promosi</h1>
                <p class="text-gray-500 text-sm mt-1">Upload banner untuk slider halaman utama & galeri profil.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-10 border-t-4 border-primary">
            <h3 class="font-bold text-lg mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-primary"></i> Tambah Banner Baru
            </h3>
            
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="block text-sm font-bold mb-2 text-gray-700">Judul Promo / Kegiatan</label>
                    <input type="text" name="judul" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" placeholder="Contoh: Diskon Lebaran" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 text-gray-700">File Gambar (Landscape)</label>
                    <input type="file" name="foto" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer" required>
                </div>
                <div>
                    <button type="submit" name="upload" class="w-full bg-primary text-white px-6 py-2.5 rounded-lg font-bold hover:bg-primaryHover shadow-lg transition transform hover:-translate-y-0.5">
                        <i class="fas fa-upload mr-2"></i> Upload Banner
                    </button>
                </div>
            </form>
        </div>

        <div class="mb-6">
            <h3 class="font-bold text-xl mb-4 text-gray-700 border-l-4 border-primary pl-3">Daftar Banner Aktif</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
            if(mysqli_num_rows($banners) > 0):
                while($b = mysqli_fetch_assoc($banners)): 
            ?>
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group border border-gray-100">
                <div class="h-48 overflow-hidden bg-gray-100 relative">
                    <img src="../assets/img/<?= $b['gambar'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition"></div>
                </div>
                
                <div class="p-5">
                    <h4 class="font-bold text-gray-800 text-lg mb-1 truncate"><?= $b['judul'] ?></h4>
                    <p class="text-xs text-gray-400 mb-4">Diupload: Banner ID #<?= $b['id'] ?></p>
                    
                    <div class="flex gap-3">
                        <button onclick="hapusBanner(<?= $b['id'] ?>)" class="flex-1 bg-red-50 text-red-600 py-2 rounded-lg text-center font-bold border border-red-100 hover:bg-red-600 hover:text-white transition shadow-sm">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-span-1 md:col-span-3 text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <i class="fas fa-images text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">Belum ada banner yang diupload.</p>
                    <p class="text-sm text-gray-400">Silakan upload gambar baru melalui form di atas.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Cek Notifikasi dari PHP
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Fungsi Konfirmasi Hapus
        function hapusBanner(id) {
            Swal.fire({
                title: 'Yakin hapus banner?',
                text: "Banner akan dihapus dari slider halaman depan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?hapus=" + id;
                }
            })
        }
    </script>
</body>
</html>