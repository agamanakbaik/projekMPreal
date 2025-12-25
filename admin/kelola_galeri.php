<?php
session_start();
include '../config/koneksi.php';

// Cek Login (Semua Admin boleh akses)
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login");
    exit;
}

// --- LOGIKA PHP DENGAN SESSION NOTIFIKASI ---

// 1. PROSES TAMBAH FOTO
if (isset($_POST['simpan'])) {
    $judul    = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']); 
    
    // Upload Gambar
    $nama_file   = $_FILES['gambar']['name'];
    $tmp_name    = $_FILES['gambar']['tmp_name'];
    $ekstensi    = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $valid_ext   = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ekstensi, $valid_ext)) {
        $nama_baru = 'galeri_' . time() . '_' . rand(100,999) . '.' . $ekstensi;
        if(move_uploaded_file($tmp_name, '../assets/img/' . $nama_baru)){
            $insert = mysqli_query($conn, "INSERT INTO gallery (judul, kategori, gambar) VALUES ('$judul', '$kategori', '$nama_baru')");
            
            if ($insert) {
                $_SESSION['notif'] = ['type' => 'success', 'text' => 'Foto berhasil ditambahkan!'];
            } else {
                $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menyimpan ke database.'];
            }
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal mengupload file gambar.'];
        }
    } else {
        $_SESSION['notif'] = ['type' => 'warning', 'text' => 'Format file harus JPG, PNG, atau WEBP!'];
    }
    header("Location: kelola_galeri");
    exit;
}

// 2. PROSES HAPUS FOTO
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($conn, "SELECT gambar FROM gallery WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);
    
    if ($d) {
        if (file_exists('../assets/img/' . $d['gambar'])) {
            unlink('../assets/img/' . $d['gambar']);
        }
        $delete = mysqli_query($conn, "DELETE FROM gallery WHERE id='$id'");
        
        if($delete){
            $_SESSION['notif'] = ['type' => 'success', 'text' => 'Foto berhasil dihapus!'];
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus data.'];
        }
    }
    header("Location: kelola_galeri");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Galeri & Mitra</title>
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
                <h1 class="text-3xl font-bold text-gray-800">Kelola Galeri & Mitra</h1>
                <p class="text-gray-500 text-sm mt-1">Upload foto kegiatan, mitra, atau promo khusus.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow-lg h-fit border-t-4 border-primary">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-primary"></i> Tambah Foto Baru
                </h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul / Nama Mitra</label>
                        <input type="text" name="judul" class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-primary/50 focus:outline-none transition" required placeholder="Contoh: Gathering Akbar / Toko A">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori / Tema</label>
                        <input type="text" list="kategori_list" name="kategori" class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-primary/50 focus:outline-none transition" required placeholder="Ketik kategori baru atau pilih...">
                        <datalist id="kategori_list">
                            <?php 
                            $q_kat = mysqli_query($conn, "SELECT DISTINCT kategori FROM gallery");
                            while($k = mysqli_fetch_assoc($q_kat)) {
                                echo "<option value='".$k['kategori']."'>";
                            }
                            ?>
                        </datalist>
                        <p class="text-xs text-gray-400 mt-1 italic">*Ketik "Mitra", "Promo", "Kegiatan", dll.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">File Foto</label>
                        <input type="file" name="gambar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer" required>
                    </div>

                    <button type="submit" name="simpan" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-2 rounded-lg transition shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-upload mr-2"></i> Upload Foto
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-primary">
                    <div class="p-6 border-b pb-4 bg-white sticky top-0 z-10">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-images text-primary"></i> Daftar Galeri
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 rounded-tl-lg">Foto</th>
                                    <th class="px-6 py-3">Judul</th>
                                    <th class="px-6 py-3">Kategori</th>
                                    <th class="px-6 py-3 text-center rounded-tr-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                $q_galeri = mysqli_query($conn, "SELECT * FROM gallery ORDER BY kategori ASC, id DESC");
                                if(mysqli_num_rows($q_galeri) > 0):
                                    while($g = mysqli_fetch_assoc($q_galeri)):
                                ?>
                                <tr class="bg-white hover:bg-primary/5 transition">
                                    <td class="px-6 py-4">
                                        <div class="h-16 w-16 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                            <img src="../assets/img/<?= $g['gambar'] ?>" class="w-full h-full object-cover">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800 text-base">
                                        <?= $g['judul'] ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold border border-primary/20">
                                            <?= $g['kategori'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick="hapusFoto(<?= $g['id'] ?>)" 
                                           class="w-8 h-8 inline-flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-camera text-4xl opacity-30"></i>
                                            <span>Belum ada foto galeri. Silakan upload.</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Konfigurasi Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Tampilkan Notifikasi dari Session PHP
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Konfirmasi Hapus
        function hapusFoto(id) {
            Swal.fire({
                title: 'Yakin hapus foto ini?',
                text: "Foto akan dihapus permanen dari galeri!",
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