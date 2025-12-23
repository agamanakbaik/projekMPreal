<?php
include 'config/koneksi.php';

// Logic Banner
$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'banners'");
$ada_tabel_banner = mysqli_num_rows($cek_tabel) > 0;

$q_brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY nama_brand ASC");

// Ambil Info Toko (Untuk Logo & Nama)
$q_info = mysqli_query($conn, "SELECT * FROM info_toko WHERE id = 1");
$info_toko = mysqli_fetch_assoc($q_info);

// Query Produk Awal
$query_sql = "SELECT products.*, brands.nama_brand 
              FROM products 
              LEFT JOIN brands ON products.brand_id = brands.id 
              ORDER BY products.id DESC";
$query = mysqli_query($conn, $query_sql);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $info_toko['nama_toko'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#e6f7f8', 100: '#ccefee', 500: '#09AFB5', 600: '#078d91', 700: '#066d70' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- 1. Banner Responsif (Tinggi menyesuaikan gambar) --- */
        .swiper {
            width: 100%;
            height: auto; /* Biarkan tinggi mengikuti gambar */
        }
        .swiper-slide img {
            width: 100%;
            height: auto; /* Tinggi otomatis agar proporsi terjaga */
            object-fit: contain; /* Pastikan gambar utuh */
            max-height: 500px; /* Batasi tinggi maksimal di layar besar agar tidak terlalu raksasa */
        }
        /* Di HP, biarkan tinggi bebas */
        @media (max-width: 768px) {
            .swiper-slide img {
                max-height: none; 
            }
        }

        /* --- 2. Tombol Panah Transparan & Modern --- */
        .swiper-button-next, .swiper-button-prev {
            background-color: rgba(255, 255, 255, 0.15); /* Sangat transparan */
            backdrop-filter: blur(2px); /* Efek blur */
            width: 40px !important;
            height: 40px !important;
            border-radius: 50%; /* Bulat */
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
            opacity: 0; /* Sembunyi jika tidak di-hover (di desktop) */
        }
        
        /* Tampilkan panah saat mouse masuk area banner */
        .swiper:hover .swiper-button-next, 
        .swiper:hover .swiper-button-prev {
            opacity: 1;
        }

        /* Hover pada tombol panah */
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background-color: rgba(255, 255, 255, 0.8); /* Putih solid saat disentuh */
            color: #09AFB5 !important; /* Warna panah jadi tosca */
            transform: scale(1.1);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Ukuran Icon Panah */
        .swiper-button-next::after, .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: bold;
        }

        /* Dots Pagination */
        .swiper-pagination-bullet-active { background-color: #09AFB5 !important; }
        
        #gridProduk { transition: opacity 0.3s ease-in-out; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans pb-24 md:pb-0 transition-colors duration-300">

    <nav class="bg-white dark:bg-gray-800 shadow fixed w-full z-50 top-0 transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex flex-wrap md:flex-nowrap justify-between items-center gap-4">
            
            <a href="index.php" class="flex items-center gap-3 group">
                <?php 
                $logo_path = 'assets/img/' . ($info_toko['logo'] ?? '');
                if (!empty($info_toko['logo']) && file_exists($logo_path)): 
                ?>
                    <img src="<?= $logo_path ?>" alt="Logo" class="w-10 h-10 object-contain rounded-full border border-gray-200 bg-white hover:scale-105 transition shadow-sm">
                <?php else: ?>
                    <div class="w-10 h-10 bg-primary-50 rounded-full flex items-center justify-center text-primary-500 border border-primary-100 shadow-sm">
                        <i class="fas fa-store text-xl"></i> 
                    </div>
                <?php endif; ?>
                <span class="text-xl md:text-2xl font-bold text-primary-500 dark:text-primary-500 tracking-tighter uppercase whitespace-nowrap group-hover:text-primary-600 transition">
                    <?= $info_toko['nama_toko'] ?>
                </span>
            </a>

            <div class="hidden md:flex space-x-6 items-center text-sm font-medium">
                <a href="index.php" class="text-primary-500 font-bold border-b-2 border-primary-500 transition">Beranda</a>
                <a href="profil.php" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Profil Kami</a>
                <a href="#katalog" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Katalog</a>
                <a href="programpublik.php" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Program Kami</a>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto order-last md:order-none">
                <div class="relative flex-1 md:w-64">
                    <input type="text" id="searchInput" onkeyup="liveSearch()" 
                           placeholder="Cari Produk..." 
                           class="w-full pl-4 pr-10 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-gray-800 transition text-sm text-gray-700 dark:text-white">
                    <span class="absolute right-0 top-0 mt-2 mr-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-yellow-400 hover:bg-gray-200 transition">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </nav>

    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-around py-2 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="index.php" class="flex flex-col items-center justify-center w-full text-primary-500 transition">
            <i class="fas fa-home text-lg mb-1"></i><span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="profil.php" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <i class="fas fa-store text-lg mb-1"></i><span class="text-[10px] font-medium">Profil</span>
        </a>
        <a href="#katalog" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <div class="bg-primary-50 dark:bg-gray-900 p-2 rounded-full -mt-6 border-4 border-gray-50 dark:border-gray-900">
                <i class="fas fa-box-open text-primary-500 text-xl"></i>
            </div>
            <span class="text-[10px] font-medium mt-1">Katalog</span>
        </a>
        <a href="programpublik.php" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <i class="fas fa-bullhorn text-lg mb-1"></i><span class="text-[10px] font-medium">Program</span>
        </a>
    </nav>

    <header class="mt-36 md:mt-20 relative">
        <div class="container mx-auto px-0 md:px-4"> <div class="swiper mySwiper w-full rounded-none md:rounded-2xl overflow-hidden shadow-none md:shadow-lg">
                <div class="swiper-wrapper">
                    <?php
                    if ($ada_tabel_banner) {
                        $q_banner = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
                        if (mysqli_num_rows($q_banner) > 0):
                            while ($ban = mysqli_fetch_assoc($q_banner)):
                    ?>
                                <div class="swiper-slide relative">
                                    <img src="assets/img/<?= $ban['gambar'] ?>" alt="<?= $ban['judul'] ?>">
                                    <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-4 md:p-8 text-white opacity-0 hover:opacity-100 transition duration-300">
                                        <h2 class="text-lg md:text-2xl font-bold"><?= $ban['judul'] ?></h2>
                                    </div>
                                </div>
                    <?php endwhile; else: echo '<div class="swiper-slide bg-primary-700 flex items-center justify-center text-white h-48 md:h-80"><h2 class="text-xl md:text-4xl font-bold px-4 text-center">Selamat Datang di ' . $info_toko['nama_toko'] . '</h2></div>'; endif; } else { echo '<div class="swiper-slide bg-gray-800 flex items-center justify-center text-white h-48 md:h-80"><h2 class="text-lg">Silakan Buat Tabel Banner Dulu</h2></div>'; } ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </header>

    <main id="katalog" class="container mx-auto px-4 py-8 md:py-12 min-h-screen">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white border-l-4 border-primary-500 pl-4 mb-6">Katalog Produk</h2>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 no-scrollbar">
                    <input type="hidden" id="filterKategori" value="">
                    <button onclick="setKategori('')" id="btn-all" class="filter-btn active px-4 py-2 rounded-full text-sm font-bold bg-primary-500 text-white transition whitespace-nowrap">Semua</button>
                    <button onclick="setKategori('parfum')" id="btn-parfum" class="filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap">Parfum</button>
                    <button onclick="setKategori('botol')" id="btn-botol" class="filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap">Botol</button>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <select id="filterBrand" onchange="liveSearch()" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-full md:w-40 text-gray-700 dark:text-gray-200">
                        <option value="">Semua Brand</option>
                        <?php while($br = mysqli_fetch_assoc($q_brands)): ?><option value="<?= $br['id'] ?>"><?= $br['nama_brand'] ?></option><?php endwhile; ?>
                    </select>
                    <select id="filterSort" onchange="liveSearch()" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-full md:w-40 text-gray-700 dark:text-gray-200">
                        <option value="">Terbaru</option>
                        <option value="termurah">Harga Termurah</option>
                        <option value="termahal">Harga Termahal</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="gridProduk" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php 
            if (mysqli_num_rows($query) > 0): 
                while ($row = mysqli_fetch_assoc($query)): 
                    $prod_id = $row['id'];
                    $q_var = mysqli_query($conn, "SELECT * FROM product_variants WHERE product_id = '$prod_id'");
                    $harga_tampil = 0; $harga_min = 9999999999; $ketemu_preferensi = false;
                    while($v = mysqli_fetch_assoc($q_var)){
                        $h = $v['harga_jual']; $uk = strtolower($v['ukuran']);
                        if($h < $harga_min && $h > 0) $harga_min = $h;
                        if((strpos($uk, '100 ml') !== false || strpos($uk, '1 lusin') !== false) && !$ketemu_preferensi){ $harga_tampil = $h; $ketemu_preferensi = true; }
                    }
                    if(!$ketemu_preferensi && $harga_min != 9999999999) $harga_tampil = $harga_min;
            ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full group">
                    <div class="relative w-full aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                        <img src="assets/img/<?= $row['foto'] ?>" alt="<?= $row['nama_produk'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-1 right-1 bg-white/90 dark:bg-gray-900/90 px-1.5 py-0.5 text-[8px] font-bold rounded text-primary-600 dark:text-primary-500 uppercase shadow-sm"><?= $row['kategori'] ?></span>
                    </div>
                    <div class="p-2 flex-1 flex flex-col">
                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider truncate"><?= $row['nama_brand'] ?? 'General' ?></div>
                        <h3 class="text-xs font-bold text-gray-800 dark:text-white leading-tight mb-1 group-hover:text-primary-500 transition line-clamp-2 h-[2.4em]"><?= $row['nama_produk'] ?></h3>
                        <div class="mt-auto pt-1 flex items-center justify-between gap-1">
                            <span class="text-xs md:text-sm font-bold text-primary-600 dark:text-primary-400">Rp <?= number_format($harga_tampil, 0, ',', '.') ?></span>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-primary-500 hover:bg-primary-600 text-white rounded text-[10px] font-bold transition">Beli</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div class="col-span-2 md:col-span-4 lg:col-span-6 text-center py-10 text-gray-500 dark:text-gray-400"><p class="text-sm font-bold">Belum ada produk.</p></div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center py-6 mt-12 hidden md:block"><p>&copy; <?= date('Y') ?> <?= $info_toko['nama_toko'] ?>. All rights reserved.</p></footer>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", { 
            spaceBetween: 0, 
            centeredSlides: true, 
            loop: true, 
            autoHeight: true, /* Agar container menyesuaikan tinggi gambar aktif */
            autoplay: { delay: 4000, disableOnInteraction: false }, 
            pagination: { el: ".swiper-pagination", clickable: true }, 
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" } 
        });
        function toggleDarkMode() { document.documentElement.classList.toggle('dark'); if(document.documentElement.classList.contains('dark')){ localStorage.setItem('theme', 'dark'); } else { localStorage.setItem('theme', 'light'); } }
        if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark'); }
        function setKategori(kat) { document.getElementById('filterKategori').value = kat; document.querySelectorAll('.filter-btn').forEach(btn => { btn.className = "filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap"; }); let btnId = kat == '' ? 'btn-all' : 'btn-' + kat; document.getElementById(btnId).className = "filter-btn active px-4 py-2 rounded-full text-sm font-bold bg-primary-500 text-white transition whitespace-nowrap"; liveSearch(); }
        function liveSearch() { let input = document.getElementById("searchInput").value; let kat = document.getElementById("filterKategori").value; let brand = document.getElementById("filterBrand").value; let sort = document.getElementById("filterSort").value; let grid = document.getElementById("gridProduk"); grid.style.opacity = "0.5"; let params = new URLSearchParams({ keyword: input, kategori: kat, brand: brand, sort: sort }); fetch('cari_ajax.php?' + params.toString()).then(response => response.text()).then(data => { grid.innerHTML = data; grid.style.opacity = "1"; }).catch(error => console.error('Error:', error)); }
    </script>
</body>
</html>