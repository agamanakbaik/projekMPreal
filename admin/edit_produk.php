<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login");
    exit;
}

$id = $_GET['id'];

// ==================================================================
// 1. PROSES HAPUS SALAH SATU FOTO GALERI
// ==================================================================
if (isset($_GET['hapus_gambar'])) {
    $id_img = $_GET['hapus_gambar'];
    
    // Ambil nama file dulu buat dihapus dari folder
    $q_cek = mysqli_query($conn, "SELECT nama_gambar FROM product_images WHERE id='$id_img'");
    $d_cek = mysqli_fetch_assoc($q_cek);
    
    if ($d_cek) {
        $path = "../assets/img/" . $d_cek['nama_gambar'];
        if (file_exists($path)) {
            unlink($path); // Hapus file fisik
        }
        // Hapus dari database
        mysqli_query($conn, "DELETE FROM product_images WHERE id='$id_img'");
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Foto galeri berhasil dihapus!'];
    }
    header("Location: edit_produk?id=$id");
    exit;
}

// ==================================================================
// 2. PROSES UPDATE DATA UTAMA & UPLOAD GALERI BARU
// ==================================================================
if (isset($_POST['update_produk'])) {
    $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // A. UPDATE INFO DASAR & COVER
    if (!empty($_FILES['foto']['name'])) {
        $foto      = $_FILES['foto']['name'];
        $tmp_foto  = $_FILES['foto']['tmp_name'];
        $foto_baru = date('dmYHis') . '_cover_' . $foto; 
        
        // Hapus foto cover lama
        $q_lama = mysqli_query($conn, "SELECT foto FROM products WHERE id='$id'");
        $d_lama = mysqli_fetch_assoc($q_lama);
        if(file_exists("../assets/img/".$d_lama['foto'])){
            unlink("../assets/img/".$d_lama['foto']);
        }

        move_uploaded_file($tmp_foto, "../assets/img/$foto_baru");
        $query = "UPDATE products SET nama_produk='$nama', deskripsi='$deskripsi', foto='$foto_baru' WHERE id='$id'";
    } else {
        $query = "UPDATE products SET nama_produk='$nama', deskripsi='$deskripsi' WHERE id='$id'";
    }
    mysqli_query($conn, $query);

    // B. UPLOAD GALERI TAMBAHAN (JIKA ADA)
    if(isset($_FILES['foto_galeri']) && !empty($_FILES['foto_galeri']['name'][0])) {
        $total_files = count($_FILES['foto_galeri']['name']);
        
        for($i = 0; $i < $total_files; $i++) {
            $nama_file = $_FILES['foto_galeri']['name'][$i];
            $tmp_file  = $_FILES['foto_galeri']['tmp_name'][$i];
            $error     = $_FILES['foto_galeri']['error'][$i];

            if($error === 0) {
                // Beri nama unik
                $nama_baru_galeri = time() . '_' . $i . '_' . $nama_file;
                $tujuan = '../assets/img/' . $nama_baru_galeri;

                if(move_uploaded_file($tmp_file, $tujuan)) {
                    mysqli_query($conn, "INSERT INTO product_images (product_id, nama_gambar) VALUES ('$id', '$nama_baru_galeri')");
                }
            }
        }
    }

    $_SESSION['notif'] = ['type' => 'success', 'text' => 'Produk & Galeri Berhasil Diupdate!'];
    header("Location: edit_produk?id=$id");
    exit;
}

// ==================================================================
// 3. PROSES TAMBAH VARIAN
// ==================================================================
if (isset($_POST['tambah_varian'])) {
    $ukuran = mysqli_real_escape_string($conn, $_POST['ukuran']);
    $harga  = $_POST['harga_varian'];
    
    if(mysqli_query($conn, "INSERT INTO product_variants (product_id, ukuran, harga_jual) VALUES ('$id', '$ukuran', '$harga')")){
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Varian Baru Ditambahkan!'];
    }
    header("Location: edit_produk?id=$id");
    exit;
}

// ==================================================================
// 4. PROSES HAPUS VARIAN
// ==================================================================
if (isset($_GET['hapus_varian'])) {
    $id_var = $_GET['hapus_varian'];
    mysqli_query($conn, "DELETE FROM product_variants WHERE id='$id_var'");
    $_SESSION['notif'] = ['type' => 'success', 'text' => 'Varian Berhasil Dihapus!'];
    header("Location: edit_produk?id=$id");
    exit;
}

// ==================================================================
// AMBIL SEMUA DATA
// ==================================================================
// 1. Data Produk
$q_prod = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$data   = mysqli_fetch_assoc($q_prod);

// 2. Data Varian
$q_var  = mysqli_query($conn, "SELECT * FROM product_variants WHERE product_id='$id' ORDER BY harga_jual ASC");

// 3. Data Galeri Foto
$q_galeri = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id='$id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Produk - <?= $data['nama_produk'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { primary: '#09AFB5', primaryHover: '#078d91' } }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="produk" class="text-sm text-gray-500 hover:text-primary mb-1 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Edit Produk</h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg border-t-4 border-primary p-6">
                    <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">
                        <i class="fas fa-edit text-primary mr-2"></i> Informasi Utama
                    </h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                            <input type="text" name="nama" value="<?= $data['nama_produk'] ?>" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="5" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50"><?= $data['deskripsi'] ?></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Foto Utama (Cover)</label>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-lg border p-1 bg-gray-50 shrink-0">
                                    <?php if(!empty($data['foto'])): ?>
                                        <img src="../assets/img/<?= $data['foto'] ?>" class="w-full h-full object-cover rounded">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="w-full">
                                    <input type="file" name="foto" class="w-full border px-3 py-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                    <p class="text-xs text-gray-400 mt-1">*Upload untuk mengganti cover halaman depan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Galeri Tambahan (Slider)</label>
                            
                            <div class="grid grid-cols-4 md:grid-cols-5 gap-3 mb-4">
                                <?php if(mysqli_num_rows($q_galeri) > 0): ?>
                                    <?php while($g = mysqli_fetch_assoc($q_galeri)): ?>
                                        <div class="relative group aspect-square bg-white rounded-lg border overflow-hidden">
                                            <?php $ext = strtolower(pathinfo($g['nama_gambar'], PATHINFO_EXTENSION)); ?>
                                            
                                            <?php if(in_array($ext, ['mp4','webm'])): ?>
                                                <video src="../assets/img/<?= $g['nama_gambar'] ?>" class="w-full h-full object-cover"></video>
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 text-white text-xs"><i class="fas fa-video"></i></div>
                                            <?php else: ?>
                                                <img src="../assets/img/<?= $g['nama_gambar'] ?>" class="w-full h-full object-cover">
                                            <?php endif; ?>

                                            <a href="javascript:void(0)" onclick="hapusGambar(<?= $g['id'] ?>)" 
                                               class="absolute top-1 right-1 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition shadow hover:bg-red-600">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="col-span-4 text-xs text-gray-400 italic">Belum ada foto tambahan.</p>
                                <?php endif; ?>
                            </div>

                            <div class="w-full">
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Tambah Foto/Video Baru:</label>
                                <input type="file" name="foto_galeri[]" multiple 
                                    class="w-full border px-3 py-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">*Tahan tombol <b>CTRL</b> untuk memilih banyak file sekaligus.</p>
                            </div>
                        </div>

                        <button type="submit" name="update_produk" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-3 rounded-lg shadow transition transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border-t-4 border-blue-500 p-6 h-full flex flex-col">
                    <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">
                        <i class="fas fa-tags text-blue-500 mr-2"></i> Varian & Harga
                    </h2>

                    <div class="flex-1 overflow-y-auto mb-4 custom-scrollbar max-h-[300px]">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600 sticky top-0">
                                <tr>
                                    <th class="p-2 text-left">Ukuran</th>
                                    <th class="p-2 text-right">Harga</th>
                                    <th class="p-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(mysqli_num_rows($q_var) > 0): ?>
                                    <?php while($v = mysqli_fetch_assoc($q_var)): ?>
                                    <tr class="hover:bg-blue-50 transition group">
                                        <td class="p-2 font-medium text-gray-700"><?= $v['ukuran'] ?></td>
                                        <td class="p-2 text-right font-bold text-primary">Rp <?= number_format($v['harga_jual'],0,',','.') ?></td>
                                        <td class="p-2 text-center">
                                            <a href="javascript:void(0)" onclick="hapusVarian(<?= $v['id'] ?>)" class="text-red-300 hover:text-red-600 transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-400 italic">Belum ada varian.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mt-auto">
                        <h3 class="text-xs font-bold text-blue-800 uppercase mb-2">Tambah Varian Baru</h3>
                        <form method="POST">
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input type="text" name="ukuran" placeholder="Ukuran (Cth: 100ml)" class="border border-blue-200 p-2 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                                <input type="number" name="harga_varian" placeholder="Harga (Rp)" class="border border-blue-200 p-2 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                            </div>
                            <button type="submit" name="tambah_varian" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 rounded shadow transition">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Konfigurasi Toast SweetAlert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Menampilkan Notifikasi dari PHP Session
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Fungsi Konfirmasi Hapus Varian
        function hapusVarian(id) {
            Swal.fire({
                title: 'Hapus Varian?',
                text: "Data varian ini akan hilang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?id=<?= $id ?>&hapus_varian=" + id;
                }
            })
        }

        // Fungsi Konfirmasi Hapus Gambar Galeri
        function hapusGambar(id) {
            Swal.fire({
                title: 'Hapus Foto?',
                text: "Foto ini akan dihapus dari galeri!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?id=<?= $id ?>&hapus_gambar=" + id;
                }
            })
        }
    </script>

</body>
</html>