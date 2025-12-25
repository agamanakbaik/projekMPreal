<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login");
    exit;
}

// --- LOGIKA PHP DENGAN SESSION NOTIFIKASI ---

// 1. UPDATE NO WA DEFAULT
if (isset($_POST['update_wa'])) {
    $no_wa_baru = mysqli_real_escape_string($conn, $_POST['no_wa_default']);
    $update = mysqli_query($conn, "UPDATE info_toko SET no_wa = '$no_wa_baru'");

    if ($update) {
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Nomor WA Default Berhasil Diupdate!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal mengupdate nomor WA.'];
    }
    header("Location: program");
    exit;
}

// 2. UPDATE HEADER BACKGROUND
if (isset($_POST['simpan_header'])) {
    if (!empty($_FILES['header_img']['name'])) {
        $foto = $_FILES['header_img']['name'];
        $tmp_foto = $_FILES['header_img']['tmp_name'];
        $ekstensi = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ekstensi, $valid)) {
            $foto_baru = 'header_program_' . time() . '.' . $ekstensi;

            // Hapus foto lama jika ada
            $q_lama = mysqli_query($conn, "SELECT header_program FROM info_toko WHERE id=1");
            $d_lama = mysqli_fetch_assoc($q_lama);
            if (isset($d_lama['header_program']) && !empty($d_lama['header_program']) && file_exists("../assets/img/" . $d_lama['header_program'])) {
                unlink("../assets/img/" . $d_lama['header_program']);
            }

            if (move_uploaded_file($tmp_foto, "../assets/img/" . $foto_baru)) {
                $update_header = mysqli_query($conn, "UPDATE info_toko SET header_program = '$foto_baru' WHERE id = 1");
                if ($update_header) {
                    $_SESSION['notif'] = ['type' => 'success', 'text' => 'Background Header Berhasil Diganti!'];
                } else {
                    $_SESSION['notif'] = ['type' => 'warning', 'text' => 'Gagal update database.'];
                }
            }
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'text' => 'Format file tidak valid (Gunakan JPG/PNG/WEBP)'];
        }
    }
    header("Location: program");
    exit;
}

// 3. TAMBAH PROGRAM
if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    $insert_prog = mysqli_query($conn, "INSERT INTO programs (judul, deskripsi, no_hp) VALUES ('$judul', '$deskripsi', '$no_hp')");

    if ($insert_prog) {
        $program_id = mysqli_insert_id($conn);
        if (!empty($_FILES['gambar']['name'][0])) {
            $jumlah_file = count($_FILES['gambar']['name']);
            for ($i = 0; $i < $jumlah_file; $i++) {
                $nama_file = $_FILES['gambar']['name'][$i];
                $tmp_name = $_FILES['gambar']['tmp_name'][$i];
                $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
                $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ekstensi, $valid_ext)) {
                    $nama_baru = 'prog_' . $program_id . '_' . time() . '_' . $i . '.' . $ekstensi;
                    if (move_uploaded_file($tmp_name, '../assets/img/' . $nama_baru)) {
                        mysqli_query($conn, "INSERT INTO program_images (program_id, gambar) VALUES ('$program_id', '$nama_baru')");
                    }
                }
            }
        }
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Program Berhasil Ditambahkan!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menyimpan program.'];
    }
    header("Location: program");
    exit;
}

// 4. HAPUS PROGRAM
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Hapus Foto Fisik
    $q_img = mysqli_query($conn, "SELECT gambar FROM program_images WHERE program_id='$id'");
    while ($img = mysqli_fetch_assoc($q_img)) {
        if (file_exists('../assets/img/' . $img['gambar'])) {
            unlink('../assets/img/' . $img['gambar']);
        }
    }

    // Hapus Data Database
    mysqli_query($conn, "DELETE FROM program_images WHERE program_id='$id'");
    $delete = mysqli_query($conn, "DELETE FROM programs WHERE id='$id'");

    if ($delete) {
        $_SESSION['notif'] = ['type' => 'success', 'text' => 'Program berhasil dihapus!'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus data.'];
    }
    header("Location: program");
    exit;
}

// Ambil Data Info Toko
$q_info = mysqli_query($conn, "SELECT * FROM info_toko LIMIT 1");
$d_info = mysqli_fetch_assoc($q_info);
$wa_default_db = $d_info['no_wa'] ?? '';
$header_program_db = $d_info['header_program'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kelola Program Kami</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
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
    <style>
        .swiper {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e5e7eb;
        }

        /* --- LOGIKA TAMPILAN GAMBAR --- */

        /* 1. Default (HP/Mobile): Gunakan CONTAIN agar utuh dan tidak terpotong */
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* 2. Layar Besar (Desktop/Laptop): Gunakan COVER agar full dan rapi */
        @media (min-width: 768px) {
            .swiper-slide img {
                object-fit: cover;
            }
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #09AFB5;
            transform: scale(0.6);
            text-shadow: 0 0 2px white;
        }

        .swiper-pagination-bullet-active {
            background: #09AFB5;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Program & Layanan</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola program, foto slider, dan kontak person.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="space-y-8">

                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-yellow-500">
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i> Kontak Utama (Default)
                    </h2>
                    <form method="POST" class="flex gap-2">
                        <input type="number" name="no_wa_default" value="<?= $wa_default_db ?>"
                            class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:outline-none transition font-bold text-gray-700"
                            placeholder="628xxx" required>
                        <button type="submit" name="update_wa"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-bold transition shadow-sm">
                            <i class="fas fa-save"></i>
                        </button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-primary">
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-image text-primary text-xl"></i> Banner Header Publik
                    </h2>

                    <div
                        class="w-full h-32 bg-gray-200 rounded-lg overflow-hidden mb-4 border border-gray-300 flex items-center justify-center relative">
                        <?php if (!empty($header_program_db) && file_exists("../assets/img/" . $header_program_db)): ?>
                            <img src="../assets/img/<?= $header_program_db ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="text-center text-gray-400">
                                <i class="fas fa-images text-2xl mb-1"></i>
                                <p class="text-xs">Belum ada banner</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                        <input type="file" name="header_img" class="w-full text-sm text-gray-500 
                      file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 
                      file:text-xs file:font-semibold 
                      file:bg-primary/10 file:text-primary 
                      hover:file:bg-primary/20 
                      transition cursor-pointer border rounded-lg" required>

                        <button type="submit" name="simpan_header"
                            class="w-full bg-primary hover:bg-primaryHover text-white py-2 rounded-lg font-bold transition shadow-sm text-sm">
                            <i class="fas fa-upload mr-2"></i> Upload Banner
                        </button>
                    </form>
                    <p class="text-[10px] text-gray-400 mt-2 italic">*Gambar ini akan menjadi background header di
                        halaman Program publik.</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg h-fit border-t-4 border-primary">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-primary"></i> Tambah Program
                    </h2>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Judul Program</label>
                            <input type="text" name="judul"
                                class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-primary/50 focus:outline-none transition"
                                required placeholder="Contoh: Paket Usaha Reseller">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp Admin
                                (Opsional)</label>
                            <input type="number" name="no_hp"
                                class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-primary/50 focus:outline-none transition"
                                placeholder="62812xxxx">
                            <p class="text-xs text-gray-400 mt-1">*Kosongkan untuk memakai Kontak Utama.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                            <textarea name="deskripsi" rows="6"
                                class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-primary/50 focus:outline-none transition"
                                required placeholder="Jelaskan detail program di sini..."></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Foto Ilustrasi</label>
                            <input type="file" name="gambar[]" multiple
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer"
                                required>
                            <p class="text-xs text-gray-400 mt-1">*Bisa pilih banyak foto sekaligus.</p>
                        </div>

                        <button type="submit" name="simpan"
                            class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-2.5 rounded-lg transition shadow-md transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Program
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-list-alt text-primary text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-700">Daftar Program Aktif</h3>
                </div>

                <div class="space-y-6">
                    <?php
                    $q_prog = mysqli_query($conn, "SELECT * FROM programs ORDER BY id DESC");
                    if (mysqli_num_rows($q_prog) > 0):
                        while ($p = mysqli_fetch_assoc($q_prog)):
                            $pid = $p['id'];
                            $q_imgs = mysqli_query($conn, "SELECT * FROM program_images WHERE program_id='$pid'");
                            $total_img = mysqli_num_rows($q_imgs);
                            ?>

                            <div
                                class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 overflow-hidden border border-gray-100 flex flex-col md:flex-row min-h-[250px]">

                                <div class="w-full md:w-5/12 bg-gray-200 relative h-64 md:h-auto overflow-hidden">
                                    <?php if ($total_img > 0): ?>
                                        <div class="swiper mySwiper h-full w-full absolute inset-0">
                                            <div class="swiper-wrapper">
                                                <?php while ($img = mysqli_fetch_assoc($q_imgs)): ?>
                                                    <div class="swiper-slide">
                                                        <img src="../assets/img/<?= $img['gambar'] ?>" loading="lazy">
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                            <?php if ($total_img > 1): ?>
                                                <div class="swiper-button-next"></div>
                                                <div class="swiper-button-prev"></div>
                                                <div class="swiper-pagination"></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="h-full flex items-center justify-center text-gray-400"><i
                                                class="fas fa-image text-4xl"></i></div>
                                    <?php endif; ?>
                                </div>

                                <div class="w-full md:w-7/12 p-5 flex flex-col">
                                    <div class="flex justify-between items-start">
                                        <h3
                                            class="text-xl font-bold text-gray-800 mb-1 leading-tight group-hover:text-primary transition">
                                            <?= $p['judul'] ?>
                                        </h3>
                                        <button onclick="hapusProgram(<?= $p['id'] ?>)"
                                            class="text-red-400 hover:text-red-600 p-2 bg-red-50 rounded hover:bg-red-100 transition"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <div class="mb-3 text-xs font-medium flex items-center gap-2 border-b pb-2 border-gray-100">
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded flex items-center gap-1">
                                            <i class="fab fa-whatsapp"></i>
                                            <?php if (!empty($p['no_hp'])): ?>
                                                <?= $p['no_hp'] ?> (Khusus)
                                            <?php else: ?>
                                                <?= $wa_default_db ?> (Default)
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-gray-400">• <?= $total_img ?> Foto</span>
                                    </div>

                                    <div class="text-sm text-gray-600 mb-4 line-clamp-6 leading-relaxed flex-1 text-justify">
                                        <?= nl2br($p['deskripsi']) ?>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; else: ?>
                        <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
                            <p class="text-gray-600 font-bold">Belum ada program.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 0, centeredSlides: true,
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        });

        // Config Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Fungsi Konfirmasi Hapus
        function hapusProgram(id) {
            Swal.fire({
                title: 'Yakin hapus program?',
                text: "Data dan semua foto program ini akan dihapus permanen!",
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