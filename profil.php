<?php
include 'config/koneksi.php';

// 1. AMBIL DATA PROFIL
$q_info = mysqli_query($conn, "SELECT * FROM info_toko LIMIT 1");
if (mysqli_num_rows($q_info) > 0) {
    $info_toko = mysqli_fetch_assoc($q_info);
} else {
    $info_toko = [
        'nama_toko' => 'Toko Kami',
        'no_wa' => '-',
        'alamat' => '-',
        'deskripsi' => 'Deskripsi toko belum diisi.',
        'logo' => '',
        'link_fb' => '',
        'link_ig' => '',
        'link_tiktok' => '',
        'link_maps' => '',
        'header_profil' => ''
    ];
}

// 2. AMBIL GALERI
$q_galeri = mysqli_query($conn, "SELECT * FROM gallery ORDER BY kategori ASC, id DESC");
$galeri_data = [];
while ($g = mysqli_fetch_assoc($q_galeri)) {
    $galeri_data[$g['kategori']][] = $g;
}

// Logic Gambar Header
$bg_header = '';
if (!empty($info_toko['header_profil']) && file_exists('assets/img/' . $info_toko['header_profil'])) {
    $bg_header = 'assets/img/' . $info_toko['header_profil'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kami - <?= $info_toko['nama_toko'] ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { primary: { 50: '#e6fbfb', 500: '#09AFB5', 600: '#078d91', 700: '#066d70' } } }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Responsif untuk Header Banner (Mirip Index) */
        .header-banner {
            width: 100%;
            height: auto;
            position: relative;
        }

        .header-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
            /* Gambar Utuh */
            max-height: 500px;
            /* Batas tinggi di desktop */
        }

        @media (max-width: 768px) {
            .header-banner img {
                max-height: none;
            }
        }
    </style>
</head>

<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans transition-colors duration-300 pb-24 md:pb-0">

    <nav class="bg-white dark:bg-gray-800 shadow fixed w-full z-50 top-0 transition-all duration-300">
        <div class="container mx-auto px-4 h-20 flex items-center justify-between gap-4">

            <a href="profil" class="flex items-center gap-3 group">
                <?php
                $logo_path = 'assets/img/' . ($info_toko['logo'] ?? '');
                if (!empty($info_toko['logo']) && file_exists($logo_path)):
                    ?>
                    <img src="<?= $logo_path ?>" alt="Logo"
                        class="w-10 h-10 object-cover rounded-full border border-gray-200 bg-white hover:scale-105 transition shadow-sm">
                <?php else: ?>
                    <div
                        class="w-10 h-10 bg-primary-50 rounded-full flex items-center justify-center text-primary-500 border border-primary-100 shadow-sm">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                <?php endif; ?>

                <span
                    class="text-xl md:text-2xl font-bold text-primary-500 dark:text-primary-500 tracking-tighter uppercase whitespace-nowrap group-hover:text-primary-600 transition">
                    <?= $info_toko['nama_toko'] ?>
                </span>
            </a>

            <div class="hidden md:flex space-x-6 items-center text-sm font-medium">
                <a href="index"
                    class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Beranda</a>
                <a href="profil" class="text-primary-500 font-bold border-b-2 border-primary-500 transition">Profil
                    Kami</a>
                <a href="index#katalog"
                    class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Katalog</a>
                <a href="programpublik"
                    class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Program Kami</a>
            </div>

            <div class="flex items-center gap-3 ml-auto md:ml-0">
                <button onclick="toggleDarkMode()"
                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-yellow-400 hover:bg-gray-200 transition shadow-sm"><i
                        class="fas fa-moon dark:hidden"></i><i class="fas fa-sun hidden dark:block"></i></button>
            </div>
        </div>
    </nav>

    <nav
        class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-around py-2 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="index"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i
                class="fas fa-home text-lg mb-1"></i><span class="text-[10px] font-medium">Beranda</span></a>
        <a href="profil" class="flex flex-col items-center justify-center w-full text-primary-500 transition"><i
                class="fas fa-store text-lg mb-1"></i><span class="text-[10px] font-medium">Profil</span></a>
        <a href="index#katalog"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full -mt-6 border-4 border-gray-50 dark:border-gray-900 group-hover:bg-primary-50 transition">
                <i class="fas fa-box-open text-gray-500 dark:text-gray-400 text-xl group-hover:text-primary-500"></i>
            </div><span class="text-[10px] font-medium mt-1">Katalog</span>
        </a>
        <a href="programpublik"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i
                class="fas fa-bullhorn text-lg mb-1"></i><span class="text-[10px] font-medium">Program</span></a>
    </nav>

    <header class="relative mt-20 w-full header-banner">
        <?php if (!empty($bg_header)): ?>
            <img src="<?= $bg_header ?>" alt="Tentang Kami" class="w-full">
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <div class="text-center px-4">
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-2 drop-shadow-lg">Tentang Kami</h1>
                    <p class="text-primary-50 text-base md:text-xl font-medium drop-shadow-md">Kenali lebih dekat perjalanan
                        <?= $info_toko['nama_toko'] ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="h-64 flex items-center justify-center bg-primary-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative z-10 text-center px-4">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2 drop-shadow-lg">Tentang Kami</h1>
                    <p class="text-primary-50 text-lg md:text-xl font-medium drop-shadow-md">Kenali lebih dekat perjalanan
                        <?= $info_toko['nama_toko'] ?></p>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <main class="container mx-auto px-4 py-12">

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-12 border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 lg:grid-cols-3">
                <div class="lg:col-span-2 p-8 md:p-12">
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white mb-6 border-l-4 border-primary-500 pl-4">
                        Siapa Kami?</h2>
                    <div
                        class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed text-justify">
                        <?= nl2br($info_toko['deskripsi']) ?>
                    </div>
                </div>
                <div
                    class="bg-primary-50 dark:bg-gray-700/30 p-8 md:p-12 border-l border-gray-100 dark:border-gray-700 flex flex-col justify-center">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 text-center">Hubungi Kami</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 shrink-0">
                                <i class="fas fa-map-marker-alt"></i></div>
                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                <?= nl2br($info_toko['alamat']) ?></p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                                <i class="fab fa-whatsapp"></i></div>
                            <a href="https://wa.me/<?= $info_toko['no_wa'] ?>" target="_blank"
                                class="text-gray-700 dark:text-gray-300 text-sm font-bold hover:text-primary-500 transition">+<?= $info_toko['no_wa'] ?></a>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p
                                class="text-xs text-gray-500 dark:text-gray-400 mb-3 text-center uppercase tracking-wide">
                                Ikuti Kami</p>
                            <div class="flex justify-center gap-4">
                                <?php if (!empty($info_toko['link_fb'])): ?>
                                    <a href="<?= $info_toko['link_fb'] ?>" target="_blank"
                                        class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:scale-110 transition shadow"><i
                                            class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($info_toko['link_ig'])): ?>
                                    <a href="<?= $info_toko['link_ig'] ?>" target="_blank"
                                        class="w-10 h-10 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white flex items-center justify-center hover:scale-110 transition shadow"><i
                                            class="fab fa-instagram"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($info_toko['link_tiktok'])): ?>
                                    <a href="<?= $info_toko['link_tiktok'] ?>" target="_blank"
                                        class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:scale-110 transition shadow"><i
                                            class="fab fa-tiktok"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-16">
            <div class="text-center mb-10">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-2">Mengapa Memilih Kami?</h2>
                <div class="w-20 h-1 bg-primary-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md text-center hover:-translate-y-2 transition duration-300 border border-gray-100 dark:border-gray-700">
                    <div
                        class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-gem"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-white">100% Bibit Murni</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kualitas bibit parfum terbaik tanpa campuran zat
                        berbahaya, wangi tahan lama.</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md text-center hover:-translate-y-2 transition duration-300 border border-gray-100 dark:border-gray-700">
                    <div
                        class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-tags"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-white">Harga Grosir</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Harga sangat bersahabat, sangat cocok untuk Anda
                        yang ingin menjual kembali (Reseller).</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md text-center hover:-translate-y-2 transition duration-300 border border-gray-100 dark:border-gray-700">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-hands-helping"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-white">Support Pemula</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bingung mulai darimana? Kami sediakan panduan
                        racik dan konsultasi gratis.</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md text-center hover:-translate-y-2 transition duration-300 border border-gray-100 dark:border-gray-700">
                    <div
                        class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-shield-alt"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-white">Garansi Aman</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pengiriman aman ke seluruh Indonesia. Barang
                        pecah? Kami ganti baru.</p>
                </div>
            </div>
        </div>

        <?php if (!empty($galeri_data)): ?>
            <?php foreach ($galeri_data as $kategori => $items): ?>
                <div class="mb-16">
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white uppercase tracking-wide">
                            <?= $kategori ?></h2>
                        <div class="h-1 flex-1 bg-primary-500/20 rounded-full"></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        <?php foreach ($items as $item): ?>
                            <div
                                class="group relative rounded-xl overflow-hidden shadow-lg bg-gray-200 dark:bg-gray-700 aspect-[4/3]">
                                <img src="assets/img/<?= $item['gambar'] ?>" alt="<?= $item['judul'] ?>"
                                    class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-4">
                                    <p
                                        class="text-white font-bold text-sm md:text-base translate-y-4 group-hover:translate-y-0 transition duration-300">
                                        <?= $item['judul'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($info_toko['link_maps'])): ?>
            <div class="mb-16">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Lokasi Toko</h2>
                </div>
                <div class="bg-gray-200 rounded-2xl overflow-hidden shadow-lg h-80 md:h-96 w-full relative z-0">
                    <?= $info_toko['link_maps'] ?>
                </div>
                <style>
                    iframe {
                        width: 100% !important;
                        height: 100% !important;
                        border: 0;
                    }
                </style>
            </div>
        <?php endif; ?>

    </main>

    <footer class="bg-gray-800 text-white text-center py-8 border-t border-gray-700 hidden md:block">
        <p class="font-medium">&copy; <?= date('Y') ?> <?= $info_toko['nama_toko'] ?>. All rights reserved.</p>
    </footer>

    <script>
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) { localStorage.setItem('theme', 'dark'); } else { localStorage.setItem('theme', 'light'); }
        }
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>

</html>