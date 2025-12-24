<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login.php");
    exit;
}

// --- LOGIKA PHP DENGAN SESSION NOTIFIKASI ---

// 1. Tambah Brand
if (isset($_POST['tambah'])) {
    $nama_brand = mysqli_real_escape_string($conn, $_POST['nama_brand']);
    
    // Cek Duplikat
    $cek = mysqli_query($conn, "SELECT id FROM brands WHERE nama_brand = '$nama_brand'");
    if(mysqli_num_rows($cek) > 0){
        $_SESSION['notif'] = ['type' => 'warning', 'text' => 'Nama Brand sudah ada!'];
    } else {
        $insert = mysqli_query($conn, "INSERT INTO brands (nama_brand) VALUES ('$nama_brand')");
        if($insert){
            $_SESSION['notif'] = ['type' => 'success', 'text' => 'Brand berhasil ditambahkan!'];
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menyimpan data.'];
        }
    }
    header("Location: brands.php");
    exit;
}

// 2. Hapus Brand
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Cek Relasi Produk
    $cek_produk = mysqli_query($conn, "SELECT id FROM products WHERE brand_id='$id'");
    if(mysqli_num_rows($cek_produk) > 0){
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal! Brand sedang dipakai oleh produk.'];
    } else {
        $delete = mysqli_query($conn, "DELETE FROM brands WHERE id='$id'");
        if($delete){
            $_SESSION['notif'] = ['type' => 'success', 'text' => 'Brand berhasil dihapus!'];
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus data.'];
        }
    }
    header("Location: brands.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Brand</title>
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
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Brand</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola daftar merek produk.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-lg h-fit border-t-4 border-primary">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-primary"></i> Tambah Brand Baru
                    </h3>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Brand</label>
                            <input type="text" name="nama_brand" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" required placeholder="Contoh: Parfex">
                        </div>
                        
                        <button type="submit" name="tambah" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-2.5 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-primary">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="p-4 border-b w-16 text-center">No</th>
                                    <th class="p-4 border-b">Nama Brand</th>
                                    <th class="p-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <?php 
                                $no = 1;
                                $brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY nama_brand ASC");
                                if(mysqli_num_rows($brands) > 0):
                                    while($b = mysqli_fetch_assoc($brands)): 
                                ?>
                                <tr class="hover:bg-primary/5 transition">
                                    <td class="p-4 text-center font-bold text-gray-500"><?= $no++ ?></td>
                                    <td class="p-4 font-bold text-gray-800 text-base"><?= $b['nama_brand'] ?></td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="edit_brand?id=<?= $b['id'] ?>" class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button onclick="hapusBrand(<?= $b['id'] ?>)" class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-tags text-3xl opacity-30"></i>
                                            <span>Belum ada data brand.</span>
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

        // Tampilkan Notifikasi dari PHP Session
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Konfirmasi Hapus
        function hapusBrand(id) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data brand akan dihapus permanen!",
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