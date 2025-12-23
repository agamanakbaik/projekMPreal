<?php
include 'config/koneksi.php';

// Ambil Data Toko (untuk Logo & Nama)
$q_info = mysqli_query($conn, "SELECT * FROM info_toko LIMIT 1");
$info_toko = mysqli_fetch_assoc($q_info);

// Ambil Data Program
$query = mysqli_query($conn, "SELECT * FROM programs ORDER BY id DESC");

// Ambil Gambar Header dari Info Toko
$bg_header = '';
if (!empty($info_toko['header_program']) && file_exists('assets/img/' . $info_toko['header_program'])) {
    $bg_header = 'assets/img/' . $info_toko['header_program'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program & Layanan - <?= $info_toko['nama_toko'] ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#e6fbfb', 500: '#09AFB5', 600: '#078d91', 700: '#066d70' }
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .swiper-pagination-bullet-active {
            background-color: #09AFB5 !important;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #fff;
            text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
            transform: scale(0.6);
        }

        .swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            /* Abu-abu terang untuk background sisa di HP */
        }

        /* --- LOGIKA GAMBAR PINTAR --- */

        /* 1. Tampilan HP (Default): Gambar UTUH (Contain) agar tidak terpotong */
        .img-program {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* 2. Tampilan Komputer (Desktop): Gambar PENUH (Cover) agar rapi full kotak */
        @media (min-width: 768px) {
            .swiper-slide {
                height: 100% !important;
            }

            .img-program {
                object-fit: cover !important;
            }
        }
    </style>
</head>

<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans flex flex-col min-h-screen transition-colors duration-300 pb-20 md:pb-0">

    <nav class="bg-white dark:bg-gray-800 shadow fixed w-full z-50 top-0 transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex flex-wrap md:flex-nowrap justify-between items-center gap-4">

            <a href="programpublik.php" class="flex items-center gap-3 group">
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
                <a href="index.php"
                    class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Beranda</a>
                <a href="profil.php" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Profil
                    Kami</a>
                <a href="index.php#katalog"
                    class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Katalog</a>
                <a href="programpublik.php"
                    class="text-primary-500 font-bold border-b-2 border-primary-500 transition">Program Kami</a>
            </div>

            <div class="flex items-center gap-3 ml-auto md:ml-0">
                <button onclick="toggleDarkMode()"
                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-yellow-400 hover:bg-gray-200 transition shadow-sm">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </nav>

    <nav
        class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-around py-2 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="index.php"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i
                class="fas fa-home text-lg mb-1"></i><span class="text-[10px] font-medium">Beranda</span></a>
        <a href="profil.php"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i
                class="fas fa-store text-lg mb-1"></i><span class="text-[10px] font-medium">Profil</span></a>
        <a href="index.php#katalog"
            class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full -mt-6 border-4 border-gray-50 dark:border-gray-900 group-hover:bg-primary-50 transition">
                <i class="fas fa-box-open text-gray-500 dark:text-gray-400 text-xl group-hover:text-primary-500"></i>
            </div><span class="text-[10px] font-medium mt-1">Katalog</span>
        </a>
        <a href="programpublik.php"
            class="flex flex-col items-center justify-center w-full text-primary-500 transition"><i
                class="fas fa-bullhorn text-lg mb-1"></i><span class="text-[10px] font-medium">Program</span></a>
    </nav>

    <header class="relative mt-20 h-72 flex items-center justify-center bg-gray-900 overflow-hidden">
        <?php if (!empty($bg_header) && file_exists($bg_header)): ?>
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= $bg_header ?>');"></div>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>
        <?php else: ?>
            <div class="absolute inset-0 bg-primary-700"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-primary-900 to-primary-600 opacity-90"></div>
        <?php endif; ?>

        <div class="relative z-10 text-center px-4">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-3 drop-shadow-lg tracking-tight">Program &
                Layanan</h1>
            <p class="text-gray-100 text-base md:text-lg font-medium max-w-2xl mx-auto drop-shadow-md leading-relaxed">
                Temukan berbagai penawaran menarik, paket usaha, dan layanan terbaik eksklusif untuk Anda.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12 flex-1">
        <?php if (mysqli_num_rows($query) > 0): ?>

            <div class="max-w-5xl mx-auto space-y-16">
                <?php
                while ($p = mysqli_fetch_assoc($query)):
                    $id_prog = $p['id'];
                    $q_img = mysqli_query($conn, "SELECT gambar FROM program_images WHERE program_id='$id_prog'");
                    $images = [];
                    while ($img = mysqli_fetch_assoc($q_img)) {
                        $images[] = $img['gambar'];
                    }

                    $nomor_tujuan = !empty($p['no_hp']) ? $p['no_hp'] : $info_toko['no_wa'];
                    ?>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 overflow-hidden flex flex-col md:flex-row border border-gray-100 dark:border-gray-700 group">

                        <div class="w-full md:w-5/12 bg-gray-100 dark:bg-gray-700 relative min-h-[300px] md:min-h-[400px]">
                            <?php if (count($images) > 0): ?>
                                <div class="swiper mySwiperCard w-full h-full absolute inset-0">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($images as $gambar): ?>
                                            <div class="swiper-slide">
                                                <img src="assets/img/<?= $gambar ?>" alt="<?= $p['judul'] ?>" class="img-program">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($images) > 1): ?>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-pagination"></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-5xl opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="w-full md:w-7/12 p-8 md:p-10 flex flex-col justify-center relative">
                            <div
                                class="absolute top-0 right-0 w-24 h-24 bg-primary-50 dark:bg-gray-700 rounded-bl-full -z-0 opacity-50">
                            </div>

                            <span
                                class="inline-block px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300 text-[10px] font-bold uppercase tracking-wider rounded-full mb-4 w-fit relative z-10">
                                Info Program
                            </span>

                            <h3
                                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-4 leading-tight relative z-10">
                                <?= $p['judul'] ?>
                            </h3>

                            <div
                                class="prose dark:prose-invert text-sm md:text-base text-gray-600 dark:text-gray-300 leading-relaxed mb-8 text-justify relative z-10">
                                <?= nl2br($p['deskripsi']) ?>
                            </div>

                            <div class="mt-auto relative z-10 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <a href="https://wa.me/<?= $nomor_tujuan ?>?text=Halo Admin, saya tertarik dengan program: *<?= $p['judul'] ?>*. Mohon infonya lebih lanjut."
                                    target="_blank"
                                    class="inline-flex items-center justify-center w-full md:w-auto px-8 py-3.5 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold transition shadow-lg hover:shadow-green-500/30 transform hover:-translate-y-1 text-sm md:text-base">
                                    <i class="fab fa-whatsapp mr-2 text-xl"></i> Hubungi Admin Sekarang
                                </a>
                                <p class="text-xs text-gray-400 mt-2 text-center md:text-left italic">*Klik tombol untuk
                                    terhubung langsung ke WhatsApp</p>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div
                    class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6 text-gray-300 animate-pulse">
                    <i class="fas fa-clipboard-list text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 dark:text-gray-400 mb-2">Belum ada program aktif</h3>
                <p class="text-gray-500 dark:text-gray-500 max-w-md mx-auto">Kami sedang menyiapkan program menarik untuk
                    Anda. Nantikan update terbaru dari kami segera!</p>
                <a href="index.php"
                    class="mt-8 px-6 py-2.5 rounded-full border-2 border-primary-500 text-primary-500 font-bold hover:bg-primary-500 hover:text-white transition">
                    Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white text-center py-8 mt-auto hidden md:block">
        <p class="text-sm opacity-75">&copy; <?= date('Y') ?> <span
                class="font-bold text-white"><?= $info_toko['nama_toko'] ?></span>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiperCard", {
            spaceBetween: 0,
            centeredSlides: true,
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

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