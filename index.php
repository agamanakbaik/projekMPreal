<?php
include 'config/koneksi.php';

// 1. LOGIC BANNER
$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'banners'");
$ada_tabel_banner = mysqli_num_rows($cek_tabel) > 0;

// 2. AMBIL DATA FILTER
$q_brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY nama_brand ASC");
$q_categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");

// 3. AMBIL INFO TOKO
$q_info = mysqli_query($conn, "SELECT * FROM info_toko WHERE id = 1");
$info_toko = mysqli_fetch_assoc($q_info);

// 4. QUERY PRODUK AWAL
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
        .swiper { width: 100%; height: auto; }
        .swiper-slide img { width: 100%; height: auto; object-fit: contain; max-height: 500px; }
        @media (max-width: 768px) { .swiper-slide img { max-height: none; } }

        .swiper-button-next, .swiper-button-prev {
            width: 40px !important; height: 40px !important;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            color: white !important;
            transition: all 0.3s ease;
            opacity: 0;
        }
        .swiper:hover .swiper-button-next, .swiper:hover .swiper-button-prev { opacity: 1; }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background-color: rgba(9, 175, 181, 0.9);
            transform: scale(1.1);
        }
        .swiper-button-next::after, .swiper-button-prev::after { font-size: 14px !important; font-weight: bold; }
        .swiper-pagination-bullet-active { background-color: #09AFB5 !important; }

        #gridProduk { transition: opacity 0.3s ease-in-out; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans pb-24 md:pb-0 transition-colors duration-300">

    <nav class="bg-white dark:bg-gray-800 shadow fixed w-full z-50 top-0 transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex flex-wrap md:flex-nowrap justify-between items-center gap-4">
            <a href="index" class="flex items-center gap-3 group">
                <?php 
                $logo_path = 'assets/img/' . ($info_toko['logo'] ?? '');
                if (!empty($info_toko['logo']) && file_exists($logo_path)): 
                ?>
                    <img src="<?= $logo_path ?>" alt="Logo" class="w-10 h-10 object-cover rounded-full border border-gray-200 bg-white hover:scale-105 transition shadow-sm">
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
                <a href="index" class="text-primary-500 font-bold border-b-2 border-primary-500 transition">Beranda</a>
                <a href="profil" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Profil Kami</a>
                <a href="#katalog" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Katalog</a>
                <a href="programpublik" class="text-gray-600 dark:text-gray-300 hover:text-primary-500 transition">Program Kami</a>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto order-last md:order-none">
                <div class="relative flex-1 md:w-64">
                    <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari Produk..." class="w-full pl-4 pr-10 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-gray-800 transition text-sm text-gray-700 dark:text-white">
                    <span class="absolute right-0 top-0 mt-2 mr-3 text-gray-400"><i class="fas fa-search"></i></span>
                </div>
                <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-yellow-400 hover:bg-gray-200 transition">
                    <i class="fas fa-moon dark:hidden"></i><i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </nav>

    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-around py-2 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="index" class="flex flex-col items-center justify-center w-full text-primary-500 transition"><i class="fas fa-home text-lg mb-1"></i><span class="text-[10px] font-medium">Beranda</span></a>
        <a href="profil" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i class="fas fa-store text-lg mb-1"></i><span class="text-[10px] font-medium">Profil</span></a>
        <a href="#katalog" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition">
            <div class="bg-primary-50 dark:bg-gray-900 p-2 rounded-full -mt-6 border-4 border-gray-50 dark:border-gray-900"><i class="fas fa-box-open text-primary-500 text-xl"></i></div><span class="text-[10px] font-medium mt-1">Katalog</span>
        </a>
        <a href="programpublik" class="flex flex-col items-center justify-center w-full text-gray-500 dark:text-gray-400 hover:text-primary-500 transition"><i class="fas fa-bullhorn text-lg mb-1"></i><span class="text-[10px] font-medium">Program</span></a>
    </nav>

    <header class="mt-36 md:mt-20 relative">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php
                if ($ada_tabel_banner) {
                    $q_banner = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
                    if (mysqli_num_rows($q_banner) > 0):
                        while ($ban = mysqli_fetch_assoc($q_banner)):
                ?>
                            <div class="swiper-slide relative">
                                <img src="assets/img/<?= $ban['gambar'] ?>" alt="<?= $ban['judul'] ?>">
                                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 to-transparent p-6 md:p-10 text-white">
                                    <h2 class="text-xl md:text-3xl font-bold"><?= $ban['judul'] ?></h2>
                                </div>
                            </div>
                <?php endwhile; else: echo '<div class="swiper-slide bg-primary-700 flex items-center justify-center text-white h-64"><h2 class="text-2xl md:text-4xl font-bold px-4 text-center">Selamat Datang di ' . $info_toko['nama_toko'] . '</h2></div>'; endif; } else { echo '<div class="swiper-slide bg-gray-800 flex items-center justify-center text-white h-64"><h2 class="text-xl">Silakan Buat Tabel Banner Dulu</h2></div>'; } ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </header>

    <main id="katalog" class="container mx-auto px-4 py-8 md:py-12 min-h-screen">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white border-l-4 border-primary-500 pl-4 mb-6">Katalog Produk</h2>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-4 justify-between items-center">
                
                <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 no-scrollbar">
                    <input type="hidden" id="filterKategori" value="">
                    
                    <button onclick="setKategori('')" id="btn-all" class="filter-btn active px-4 py-2 rounded-full text-sm font-bold bg-primary-500 text-white transition whitespace-nowrap">Semua</button>
                    <button onclick="setKategori('1')" id="btn-1" class="filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap">Parfum</button>
                    <button onclick="setKategori('2')" id="btn-2" class="filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap">Botol</button>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    
                    <select id="selectKategoriLengkap" onchange="pilihDariDropdown(this.value)" 
                            class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-full md:w-40 text-gray-700 dark:text-gray-200 font-medium">
                        <option value="">Semua Kategori</option>
                        <?php 
                        if(isset($q_categories)) mysqli_data_seek($q_categories, 0);
                        while ($kat = mysqli_fetch_assoc($q_categories)): ?>
                            <option value="<?= $kat['id'] ?>"><?= $kat['nama_kategori'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <select id="filterBrand" onchange="liveSearch()" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-full md:w-40 text-gray-700 dark:text-gray-200 font-medium">
                        <option value="">Semua Brand</option>
                        <?php while($br = mysqli_fetch_assoc($q_brands)): ?><option value="<?= $br['id'] ?>"><?= $br['nama_brand'] ?></option><?php endwhile; ?>
                    </select>

                    <select id="filterSort" onchange="liveSearch()" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-full md:w-40 text-gray-700 dark:text-gray-200 font-medium">
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

                    $cat_id_awal = $row['category_id'] ?? 0;
                    $q_cat_name_awal = mysqli_query($conn, "SELECT nama_kategori FROM categories WHERE id = '$cat_id_awal'");
                    $d_cat_awal = mysqli_fetch_assoc($q_cat_name_awal);
                    $label_kategori = $d_cat_awal['nama_kategori'] ?? 'Umum';
            ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full group">
                    <div class="relative w-full aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                        <img src="assets/img/<?= $row['foto'] ?>" alt="<?= $row['nama_produk'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-1 right-1 bg-white/90 dark:bg-gray-900/90 px-1.5 py-0.5 text-[8px] font-bold rounded text-primary-600 dark:text-primary-500 uppercase shadow-sm"><?= $label_kategori ?></span>
                    </div>
                    <div class="p-2 flex-1 flex flex-col">
                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider truncate"><?= $row['nama_brand'] ?? 'General' ?></div>
                        <h3 class="text-xs font-bold text-gray-800 dark:text-white leading-tight mb-1 group-hover:text-primary-600 transition line-clamp-2 h-[2.4em]"><?= $row['nama_produk'] ?></h3>
                        <div class="mt-auto pt-1 flex items-center justify-between gap-1">
                            <span class="text-xs md:text-sm font-bold text-primary-600 dark:text-primary-400">Rp <?= number_format($harga_tampil, 0, ',', '.') ?></span>
                            <a href="detail?id=<?= $row['id'] ?>" class="px-3 py-1 bg-primary-500 hover:bg-primary-600 text-white rounded text-[10px] font-bold transition">Beli</a>
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
            spaceBetween: 0, centeredSlides: true, loop: true, 
            autoHeight: true, 
            autoplay: { delay: 4000, disableOnInteraction: false }, 
            pagination: { el: ".swiper-pagination", clickable: true }, 
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" } 
        });

        function toggleDarkMode() { document.documentElement.classList.toggle('dark'); if(document.documentElement.classList.contains('dark')){ localStorage.setItem('theme', 'dark'); } else { localStorage.setItem('theme', 'light'); } }
        if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark'); }

        // --- LOGIKA FILTER GABUNGAN (Tombol & Dropdown) ---

        // Fungsi dipanggil saat TOMBOL Kategori diklik
        function setKategori(katId) {
            // Update nilai input hidden
            document.getElementById('filterKategori').value = katId;
            
            // Sinkronkan Dropdown (agar ikut berubah sesuai tombol yg diklik)
            document.getElementById('selectKategoriLengkap').value = katId;

            // Update Style Tombol
            updateTombolStyle(katId);

            // Jalankan Pencarian
            liveSearch();
        }

        // Fungsi dipanggil saat DROPDOWN Kategori dipilih
        function pilihDariDropdown(katId) {
            // Update nilai input hidden
            document.getElementById('filterKategori').value = katId;

            // Update Style Tombol (Jika ada tombol yg cocok dengan pilihan dropdown)
            updateTombolStyle(katId);

            // Jalankan Pencarian
            liveSearch();
        }

        // Helper untuk mengubah warna tombol aktif
        function updateTombolStyle(katId) {
            // Reset semua tombol jadi abu-abu
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.className = "filter-btn px-4 py-2 rounded-full text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-50 transition whitespace-nowrap";
            });

            // Cari tombol yang ID-nya cocok
            let btnId = (katId == '') ? 'btn-all' : 'btn-' + katId;
            let activeBtn = document.getElementById(btnId);

            // Jika tombolnya ada (misal: Parfum/Botol), warnai jadi hijau/primary
            // Jika pilih kategori lain di dropdown (yg gak punya tombol), tidak ada tombol yg aktif
            if(activeBtn){
                activeBtn.className = "filter-btn active px-4 py-2 rounded-full text-sm font-bold bg-primary-500 text-white transition whitespace-nowrap";
            }
        }

        function liveSearch() { 
            let input = document.getElementById("searchInput").value; 
            let kat = document.getElementById("filterKategori").value; // Ambil nilai dari Hidden Input
            let brand = document.getElementById("filterBrand").value; 
            let sort = document.getElementById("filterSort").value; 
            
            let grid = document.getElementById("gridProduk"); 
            grid.style.opacity = "0.5"; 
            
            let params = new URLSearchParams({ keyword: input, kategori: kat, brand: brand, sort: sort }); 
            
            fetch('cari_ajax.php?' + params.toString())
                .then(response => response.text())
                .then(data => { 
                    grid.innerHTML = data; 
                    grid.style.opacity = "1"; 
                })
                .catch(error => console.error('Error:', error)); 
        }
    </script>
</body>
</html>