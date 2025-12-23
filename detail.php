<?php
include 'config/koneksi.php';

$id_produk = $_GET['id'];

// 1. UPDATE QUERY: Tambahkan LEFT JOIN ke tabel brands
$query = mysqli_query($conn, "SELECT products.*, brands.nama_brand 
                              FROM products 
                              LEFT JOIN brands ON products.brand_id = brands.id 
                              WHERE products.id='$id_produk'");
$data = mysqli_fetch_assoc($query);

// Cek jika produk tidak ditemukan (jaga-jaga error)
if (!$data) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Ambil varian ukuran (jika ada)
$query_varian = mysqli_query($conn, "SELECT * FROM product_variants WHERE product_id='$id_produk' ORDER BY harga_jual ASC");
$varian = [];
while ($row = mysqli_fetch_assoc($query_varian)) {
    $varian[] = $row;
}

// Jika tidak ada varian, pakai harga default produk
$harga_default = $data['harga_jual'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['nama_produk'] ?> - Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>

<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center py-8">

    <div class="container mx-auto px-4 max-w-5xl relative">
        
        <a href="index.php" class="absolute -top-4 -right-2 md:-right-4 z-50 bg-red-500 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition transform hover:scale-110 border-2 border-white">
            <i class="fas fa-times text-xl"></i>
        </a>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative">

            <div class="md:w-1/2 bg-gray-100 relative group">
                <img src="assets/img/<?= $data['foto'] ?>" alt="Foto Produk" class="w-full h-96 md:h-full object-cover transition duration-500 group-hover:scale-105">
                
                <span class="absolute top-4 left-4 bg-primary-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase shadow-md">
                    <?= $data['kategori'] ?>
                </span>
            </div>

            <div class="md:w-1/2 p-8 md:p-10 flex flex-col">
                
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-tag text-primary-500"></i>
                    <?= $data['nama_brand'] ?? 'General' ?>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 leading-tight">
                    <?= $data['nama_produk'] ?>
                </h1>

                <div class="prose prose-sm text-gray-600 mb-8 leading-relaxed text-justify">
                    <?= nl2br($data['deskripsi']) ?>
                </div>

                <?php if (!empty($varian)): ?>
                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-3 text-sm uppercase tracking-wide">Pilih Ukuran:</label>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach ($varian as $v): ?>
                                <button onclick="pilihVarian(this, '<?= $v['ukuran'] ?>', <?= $v['harga_jual'] ?>)"
                                    class="btn-varian border-2 border-gray-200 text-gray-600 px-4 py-2 rounded-lg font-medium hover:border-primary-500 hover:text-primary-500 transition duration-200">
                                    <?= $v['ukuran'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-auto border-t border-gray-100 pt-6">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <span class="text-gray-400 text-sm">Total Harga:</span>
                            <h2 id="displayHarga" class="text-4xl font-bold text-primary-600">
                                Rp <?= number_format($harga_default, 0, ',', '.') ?>
                            </h2>
                        </div>
                    </div>

                    <a id="btnWA" href="#" target="_blank" onclick="catatKlik(<?= $data['id'] ?>)"
                        class="block w-full bg-primary-500 text-white text-center font-bold py-4 rounded-xl shadow-lg shadow-primary-500/30 hover:bg-primary-600 hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        <span>Pesan Sekarang via WhatsApp</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Data awal
        let namaProduk = "<?= $data['nama_produk'] ?>";
        let brandProduk = "<?= $data['nama_brand'] ?? '' ?>"; 
        
        let noWA = "<?= $info_toko['no_wa'] ?>"; 
        let hargaSekarang = <?= $harga_default ?>;
        let ukuranTerpilih = "Standard";

        // Fungsi update link WA
        function updateLink() {
            let pesan = `Halo Admin, saya mau pesan:\n*${namaProduk}* (${brandProduk})\nUkuran: ${ukuranTerpilih} \nHarga: Rp ${hargaSekarang.toLocaleString('id-ID')}`;
            let link = `https://wa.me/${noWA}?text=${encodeURIComponent(pesan)}`;
            document.getElementById('btnWA').href = link;
        }

        // Fungsi saat tombol varian diklik
        function pilihVarian(el, ukuran, harga) {
            // Reset style tombol lain
            document.querySelectorAll('.btn-varian').forEach(btn => {
                // Hapus warna Primary (Tosca)
                btn.classList.remove('bg-primary-500', 'text-white', 'border-primary-500');
                // Tambah warna Gray (Default)
                btn.classList.add('border-gray-200', 'text-gray-600');
            });

            // Highlight tombol aktif (Tosca Solid)
            el.classList.remove('border-gray-200', 'text-gray-600');
            el.classList.add('bg-primary-500', 'text-white', 'border-primary-500');

            // Update data
            ukuranTerpilih = ukuran;
            hargaSekarang = harga;

            // Update tampilan harga
            document.getElementById('displayHarga').innerText = "Rp " + harga.toLocaleString('id-ID');

            updateLink();
        }

        // Jalankan sekali saat load
        updateLink();

        function catatKlik(idProduk) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "track_click.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("id=" + idProduk);
        }
    </script>
</body>

</html>