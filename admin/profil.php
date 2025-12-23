<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login.php");
    exit;
}

// --- LOGIKA SIMPAN DATA ---
if (isset($_POST['simpan'])) {
    $nama_toko = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $no_wa     = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Data Sosmed
    $link_fb     = mysqli_real_escape_string($conn, $_POST['link_fb']);
    $link_ig     = mysqli_real_escape_string($conn, $_POST['link_ig']);
    $link_tiktok = mysqli_real_escape_string($conn, $_POST['link_tiktok']);
    $link_maps   = mysqli_real_escape_string($conn, $_POST['link_maps']);

    // Update Text Data
    $update = mysqli_query($conn, "UPDATE info_toko SET 
                                   nama_toko = '$nama_toko',
                                   no_wa = '$no_wa',
                                   alamat = '$alamat',
                                   deskripsi = '$deskripsi',
                                   link_fb = '$link_fb',
                                   link_ig = '$link_ig',
                                   link_tiktok = '$link_tiktok',
                                   link_maps = '$link_maps'
                                   WHERE id = 1");

    // --- PROSES UPLOAD LOGO ---
    if (!empty($_FILES['logo']['name'])) {
        $nama_file   = $_FILES['logo']['name'];
        $tmp_name    = $_FILES['logo']['tmp_name'];
        $ekstensi    = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $valid_ext   = ['png', 'jpg', 'jpeg', 'webp'];

        if (in_array($ekstensi, $valid_ext)) {
            $q_lama = mysqli_query($conn, "SELECT logo FROM info_toko WHERE id = 1");
            $d_lama = mysqli_fetch_assoc($q_lama);
            if ($d_lama['logo'] != '' && file_exists("../assets/img/" . $d_lama['logo'])) {
                unlink("../assets/img/" . $d_lama['logo']);
            }

            $nama_baru = 'logo_' . time() . '.' . $ekstensi;
            if(move_uploaded_file($tmp_name, '../assets/img/' . $nama_baru)){
                mysqli_query($conn, "UPDATE info_toko SET logo = '$nama_baru' WHERE id = 1");
            }
        }
    }

    // --- PROSES UPLOAD HEADER PROFIL (BARU) ---
    if (!empty($_FILES['header_profil']['name'])) {
        $nama_file_h = $_FILES['header_profil']['name'];
        $tmp_name_h  = $_FILES['header_profil']['tmp_name'];
        $ekstensi_h  = strtolower(pathinfo($nama_file_h, PATHINFO_EXTENSION));
        $valid_ext   = ['png', 'jpg', 'jpeg', 'webp'];

        if (in_array($ekstensi_h, $valid_ext)) {
            $q_lama_h = mysqli_query($conn, "SELECT header_profil FROM info_toko WHERE id = 1");
            $d_lama_h = mysqli_fetch_assoc($q_lama_h);
            // Hapus file lama jika ada
            if (!empty($d_lama_h['header_profil']) && file_exists("../assets/img/" . $d_lama_h['header_profil'])) {
                unlink("../assets/img/" . $d_lama_h['header_profil']);
            }

            $nama_baru_h = 'header_profil_' . time() . '.' . $ekstensi_h;
            if(move_uploaded_file($tmp_name_h, '../assets/img/' . $nama_baru_h)){
                mysqli_query($conn, "UPDATE info_toko SET header_profil = '$nama_baru_h' WHERE id = 1");
            }
        }
    }

    $_SESSION['notif'] = ['type' => 'success', 'text' => 'Data Toko Berhasil Diupdate!'];
    header("Location: profil.php");
    exit;
}

// Ambil Data Profil Saat Ini
$cek_data = mysqli_query($conn, "SELECT * FROM info_toko WHERE id = 1");
if(mysqli_num_rows($cek_data) == 0){
    mysqli_query($conn, "INSERT INTO info_toko (id, nama_toko) VALUES (1, 'Nama Toko Anda')");
    $cek_data = mysqli_query($conn, "SELECT * FROM info_toko WHERE id = 1");
}
$d = mysqli_fetch_assoc($cek_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Identitas Toko</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#09AFB5', primaryHover: '#078d91' } } }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Identitas Toko</h1>
                <p class="text-gray-500 text-sm mt-1">Atur informasi, logo, dan banner halaman profil.</p>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center relative overflow-hidden group">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">LOGO TOKO</h3>
                    <div class="relative w-40 h-40 mx-auto group">
                        <div class="w-full h-full rounded-full border-4 border-gray-100 shadow-inner overflow-hidden bg-white flex items-center justify-center relative">
                            <?php if (!empty($d['logo']) && file_exists('../assets/img/' . $d['logo'])): ?>
                                <img src="../assets/img/<?= $d['logo'] ?>" class="w-full h-full object-cover" id="preview_logo">
                            <?php else: ?>
                                <i class="fas fa-store text-6xl text-gray-300" id="icon_default"></i>
                                <img src="" class="w-full h-full object-cover hidden" id="preview_logo">
                            <?php endif; ?>
                        </div>
                        <label class="absolute bottom-1 right-1 bg-primary hover:bg-primaryHover text-white w-10 h-10 flex items-center justify-center rounded-full shadow-lg cursor-pointer transition transform hover:scale-110 z-10 border-2 border-white" title="Ganti Logo">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" name="logo" class="hidden" onchange="previewImage(this, 'preview_logo', 'icon_default')">
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">BANNER HEADER PROFIL</h3>
                    <div class="w-full h-32 bg-gray-100 rounded-lg overflow-hidden border border-dashed border-gray-300 relative group flex items-center justify-center">
                        <?php if (!empty($d['header_profil']) && file_exists('../assets/img/' . $d['header_profil'])): ?>
                            <img src="../assets/img/<?= $d['header_profil'] ?>" class="w-full h-full object-cover" id="preview_header">
                        <?php else: ?>
                            <div class="text-center text-gray-400" id="text_header_default">
                                <i class="fas fa-image text-2xl"></i>
                                <p class="text-[10px]">No Banner</p>
                            </div>
                            <img src="" class="w-full h-full object-cover hidden" id="preview_header">
                        <?php endif; ?>
                    </div>
                    <label class="block mt-3">
                        <span class="sr-only">Choose profile photo</span>
                        <input type="file" name="header_profil" onchange="previewImageHeader(this)" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"/>
                    </label>
                    <p class="text-[10px] text-gray-400 mt-2">Gambar ini akan muncul di bagian atas halaman "Profil Kami".</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kontak & Alamat</h3>
                    <div class="mb-4">
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-1">WhatsApp Admin</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-green-500"><i class="fab fa-whatsapp text-lg"></i></span>
                            <input type="number" name="no_wa" value="<?= $d['no_wa'] ?>" class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition text-gray-700" placeholder="628..." required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="4" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm transition text-gray-700"><?= $d['alamat'] ?></textarea>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i> Detail Toko
                    </h3>
                    <div class="mb-6">
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-2">NAMA TOKO (BRAND)</label>
                        <input type="text" name="nama_toko" value="<?= $d['nama_toko'] ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-lg font-bold text-gray-800 transition shadow-sm" placeholder="Nama Toko Anda">
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs font-bold uppercase mb-2">DESKRIPSI SINGKAT (TENTANG KAMI)</label>
                        <textarea name="deskripsi" rows="5" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-gray-600 leading-relaxed transition shadow-sm"><?= $d['deskripsi'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-share-alt text-blue-500"></i> Sosial Media & Peta
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-500 text-xs font-bold mb-1">Facebook URL</label>
                            <input type="text" name="link_fb" value="<?= $d['link_fb'] ?? '' ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary/50 text-sm text-gray-600" placeholder="https://facebook.com/...">
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs font-bold mb-1">Instagram URL</label>
                            <input type="text" name="link_ig" value="<?= $d['link_ig'] ?? '' ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary/50 text-sm text-gray-600" placeholder="https://instagram.com/...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-500 text-xs font-bold mb-1">TikTok URL</label>
                            <input type="text" name="link_tiktok" value="<?= $d['link_tiktok'] ?? '' ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary/50 text-sm text-gray-600" placeholder="https://tiktok.com/...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs font-bold mb-2">Google Maps Embed Code</label>
                        <textarea name="link_maps" rows="3" class="w-full px-3 py-2 border rounded-lg text-xs font-mono bg-gray-100 focus:ring-2 focus:ring-primary/50 text-gray-500 transition" placeholder='<iframe src="http://googleusercontent.com...'> <?= $d['link_maps'] ?? '' ?></textarea>
                    </div>
                </div>

                <button type="submit" name="simpan" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1 flex justify-center items-center gap-2 text-lg border-b-4 border-[#078d91]">
                    <i class="fas fa-save"></i> SIMPAN PERUBAHAN
                </button>

            </div>
        </form>
    </main>

    <script>
        function previewImage(input, imgId, iconId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById(imgId);
                    var icon = document.getElementById(iconId);
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    if(icon) icon.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewImageHeader(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById('preview_header');
                    var txt = document.getElementById('text_header_default');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    if(txt) txt.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>
    </script>

</body>
</html>