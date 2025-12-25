<?php
include 'config/koneksi.php';

$id_produk = $_GET['id'];

// 1. AMBIL DATA PRODUK
$query = mysqli_query($conn, "SELECT products.*, 
                                     brands.nama_brand, 
                                     categories.nama_kategori 
                              FROM products 
                              LEFT JOIN brands ON products.brand_id = brands.id 
                              LEFT JOIN categories ON products.category_id = categories.id 
                              WHERE products.id='$id_produk'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index';</script>";
    exit;
}

// 2. AMBIL SEMUA MEDIA (GAMBAR & VIDEO)
$query_img = mysqli_query($conn, "SELECT nama_gambar FROM product_images WHERE product_id='$id_produk'");
$semua_media = [];

if (!empty($data['foto'])) {
    $semua_media[] = $data['foto'];
}

while ($img = mysqli_fetch_assoc($query_img)) {
    $semua_media[] = $img['nama_gambar'];
}

if (empty($semua_media)) {
    $semua_media[] = 'default.jpg'; 
}

function getFileType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
        return 'video';
    }
    return 'image';
}

// 3. AMBIL VARIAN
$query_varian = mysqli_query($conn, "SELECT * FROM product_variants WHERE product_id='$id_produk' ORDER BY harga_jual ASC");
$varian = [];
while ($row = mysqli_fetch_assoc($query_varian)) {
    $varian[] = $row;
}

$harga_default = $data['harga_jual'];
$ukuran_default = "Standard"; 
if (!empty($varian)) {
    $harga_default = $varian[0]['harga_jual'];
    $ukuran_default = $varian[0]['ukuran'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['nama_produk'] ?> - Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#e6fbfb', 500: '#09AFB5', 600: '#078d91', 700: '#066d70' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .swiper {
            width: 100%;
            height: 100%;
        }
        .swiper-pagination-bullet-active {
            background-color: #09AFB5 !important;
        }
        /* Tombol panah dipercantik */
        .swiper-button-next, .swiper-button-prev {
            color: #09AFB5 !important;
            background: rgba(255,255,255,0.9);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 14px !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center py-4 md:py-8">

    <div class="container mx-auto px-4 max-w-5xl relative">
        
        <a href="index" class="absolute -top-2 right-2 md:-top-4 md:-right-4 z-50 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center shadow-lg transition transform hover:scale-110 border-2 border-white">
            <i class="fas fa-times text-sm md:text-xl"></i>
        </a>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row relative">

            <div class="md:w-1/2 bg-gray-50 relative w-full aspect-square md:aspect-auto md:h-auto border-b md:border-b-0 md:border-r border-gray-100">
                
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        
                        <?php foreach($semua_media as $media): ?>
                            <div class="swiper-slide flex items-center justify-center bg-white">
                                
                                <?php if(getFileType($media) == 'video'): ?>
                                    <video controls class="w-full h-full object-contain max-h-[500px]">
                                        <source src="assets/img/<?= $media ?>" type="video/mp4">
                                    </video>
                                <?php else: ?>
                                    <img src="assets/img/<?= $media ?>" alt="Produk" class="w-full h-full object-contain">
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>

                    </div>
                    
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>

                <span class="absolute top-4 left-4 z-10 bg-primary-500 text-white text-[10px] md:text-xs font-bold px-3 py-1 rounded-full uppercase shadow-md">
                    <?= $data['nama_kategori'] ?? 'Umum' ?>
                </span>

            </div>

            <div class="md:w-1/2 p-6 md:p-10 flex flex-col">
                
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-tag text-primary-500"></i>
                    <?= $data['nama_brand'] ?? 'General' ?>
                </div>

                <h1 class="text-2xl md:text-4xl font-extrabold text-gray-800 mb-4 leading-tight">
                    <?= $data['nama_produk'] ?>
                </h1>

                <div class="prose prose-sm text-gray-600 mb-6 leading-relaxed text-justify max-h-40 overflow-y-auto pr-2 custom-scroll">
                    <?= nl2br($data['deskripsi']) ?>
                </div>

                <?php if (!empty($varian)): ?>
                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-3 text-sm uppercase tracking-wide">Pilih Ukuran:</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($varian as $index => $v): 
                                $isActive = ($index === 0) ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-200 text-gray-600';
                            ?>
                                <button onclick="pilihVarian(this, '<?= $v['ukuran'] ?>', <?= $v['harga_jual'] ?>)"
                                    class="btn-varian text-sm border px-3 py-2 rounded-lg font-medium hover:border-primary-500 hover:text-primary-500 transition duration-200 <?= $isActive ?>">
                                    <?= $v['ukuran'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-auto pt-4 border-t border-gray-100">
                    <div class="mb-4">
                        <span class="text-gray-400 text-xs uppercase font-bold">Harga Total:</span>
                        <h2 id="displayHarga" class="text-3xl font-bold text-primary-600">
                            Rp <?= number_format($harga_default, 0, ',', '.') ?>
                        </h2>
                    </div>

                    <a id="btnWA" href="#" target="_blank"
                        class="block w-full bg-primary-500 text-white text-center font-bold py-3.5 rounded-xl shadow-lg shadow-primary-500/30 hover:bg-primary-600 hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Beli via WhatsApp</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoHeight: false, // Kita pakai fixed aspect-square biar rapi
            autoplay: {
                delay: 4000,
                disableOnInteraction: true,
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

        // --- Logic Varian ---
        let namaProduk = "<?= $data['nama_produk'] ?>";
        let brandProduk = "<?= $data['nama_brand'] ?? '' ?>"; 
        
        // GANTI NOMOR WA DISINI JIKA BELUM TER-SET DI DATABASE
        let noWA = "62895359737004"; 
        <?php if(isset($info_toko['no_wa'])) : ?>
            noWA = "<?= $info_toko['no_wa'] ?>";
        <?php endif; ?>

        let hargaSekarang = <?= $harga_default ?>;
        let ukuranTerpilih = "<?= $ukuran_default ?>";

        function updateLink() {
            let pesan = `Halo Admin, saya mau pesan:\n*${namaProduk}* (${brandProduk})\nUkuran: ${ukuranTerpilih} \nHarga: Rp ${hargaSekarang.toLocaleString('id-ID')}`;
            let link = `https://wa.me/${noWA}?text=${encodeURIComponent(pesan)}`;
            document.getElementById('btnWA').href = link;
        }

        function pilihVarian(el, ukuran, harga) {
            document.querySelectorAll('.btn-varian').forEach(btn => {
                btn.classList.remove('bg-primary-500', 'text-white', 'border-primary-500');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });

            el.classList.remove('border-gray-200', 'text-gray-600');
            el.classList.add('bg-primary-500', 'text-white', 'border-primary-500');

            ukuranTerpilih = ukuran;
            hargaSekarang = harga;
            document.getElementById('displayHarga').innerText = "Rp " + harga.toLocaleString('id-ID');
            updateLink();
        }

        updateLink();
    </script>
</body>
</html>