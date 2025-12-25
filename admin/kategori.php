<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login");
    exit;
}

// --- LOGIKA PHP (Diubah menggunakan Session untuk Notifikasi) ---

// Tambah Kategori
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe_hitung']);
    
    $insert = mysqli_query($conn, "INSERT INTO categories (nama_kategori, tipe_hitung) VALUES ('$nama', '$tipe')");
    
    if($insert){
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Kategori berhasil ditambahkan!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menambahkan data.'];
    }
    header("Location: kategori");
    exit;
}

// Hapus Kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
    
    if($delete){
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Kategori berhasil dihapus!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus data.'];
    }
    header("Location: kategori");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kategori</title>
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
                <h1 class="text-3xl font-bold text-gray-800">Kelola Kategori</h1>
                <p class="text-gray-500 text-sm mt-1">Atur kategori produk (Volume atau Qty).</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow-lg h-fit border-t-4 border-primary">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-primary"></i> Tambah Kategori
                </h3>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" placeholder="Cth: Parfum Mobil" required>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Hitungan</label>
                        <select name="tipe_hitung" class="w-full border px-3 py-2 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary/50 transition">
                            <option value="volume">Volume (ML / Liter)</option>
                            <option value="qty">Qty (Pcs / Lusin / Karton)</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">*Pilih Volume untuk cairan, Qty untuk barang satuan.</p>
                    </div>
                    
                    <button type="submit" name="tambah" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-2.5 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-primary">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="p-4 border-b">Nama Kategori</th>
                                    <th class="p-4 border-b">Tipe</th>
                                    <th class="p-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <?php 
                                $q = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
                                if(mysqli_num_rows($q) > 0):
                                    while($row = mysqli_fetch_assoc($q)): 
                                ?>
                                <tr class="hover:bg-primary/5 transition">
                                    <td class="p-4 font-bold text-gray-800">
                                        <?= $row['nama_kategori'] ?>
                                    </td>
                                    <td class="p-4">
                                        <?php if($row['tipe_hitung'] == 'volume'): ?>
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Volume</span>
                                        <?php else: ?>
                                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Qty / Pcs</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="hapusData(<?= $row['id'] ?>)" 
                                           class="w-8 h-8 inline-flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm" 
                                           title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-folder-open text-3xl opacity-30"></i>
                                            <span>Belum ada kategori data.</span>
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
        // Definisi Toast (Alert Kecil di Pojok Kanan Atas)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end', // Posisi di kanan atas
            showConfirmButton: false, // Tidak butuh tombol OK
            timer: 3000, // Hilang otomatis dalam 3 detik
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Cek apakah ada notifikasi dari PHP
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>', // success atau error
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); // Hapus session agar tidak muncul lagi saat refresh ?>
        <?php endif; ?>

        // Fungsi Konfirmasi Hapus (Biar aman)
        function hapusData(id) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#09AFB5',
                cancelButtonColor: '#d33',
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