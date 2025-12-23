<?php
include 'config/koneksi.php';

// 1. Tangkap Parameter
$keyword  = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$brand    = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';
$sort     = isset($_GET['sort']) ? $_GET['sort'] : '';

// 2. Query Dasar
$sql = "SELECT products.*, brands.nama_brand 
        FROM products 
        LEFT JOIN brands ON products.brand_id = brands.id 
        WHERE 1=1"; 

if (!empty($keyword)) {
    $sql .= " AND (products.nama_produk LIKE '%$keyword%' OR brands.nama_brand LIKE '%$keyword%')";
}
if (!empty($kategori)) {
    $sql .= " AND products.kategori = '$kategori'";
}
if (!empty($brand)) {
    $sql .= " AND products.brand_id = '$brand'";
}

if ($sort == 'termurah') {
    $sql .= " ORDER BY products.harga_jual ASC";
} elseif ($sort == 'termahal') {
    $sql .= " ORDER BY products.harga_jual DESC";
} else {
    $sql .= " ORDER BY products.id DESC"; 
}

$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        
        // Logika Harga Pintar
        $prod_id = $row['id'];
        $q_var = mysqli_query($conn, "SELECT * FROM product_variants WHERE product_id = '$prod_id'");
        $harga_tampil = 0;
        $harga_min = 9999999999;
        $ketemu_preferensi = false;

        while($v = mysqli_fetch_assoc($q_var)){
            $h = $v['harga_jual'];
            $uk = strtolower($v['ukuran']);
            if($h < $harga_min && $h > 0) $harga_min = $h;
            if((strpos($uk, '100 ml') !== false || strpos($uk, '1 lusin') !== false) && !$ketemu_preferensi){
                $harga_tampil = $h;
                $ketemu_preferensi = true;
            }
        }
        if(!$ketemu_preferensi && $harga_min != 9999999999) $harga_tampil = $harga_min;
?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full group">
            
            <div class="relative w-full aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                <img src="assets/img/<?= $row['foto'] ?>" alt="<?= $row['nama_produk'] ?>"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                
                <span class="absolute top-1 right-1 bg-white/90 dark:bg-gray-900/90 px-1.5 py-0.5 text-[8px] font-bold rounded text-primary-600 dark:text-primary-400 uppercase shadow-sm">
                    <?= $row['kategori'] ?>
                </span>
            </div>

            <div class="p-2 flex-1 flex flex-col">
                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider truncate">
                    <?= $row['nama_brand'] ?? 'General' ?>
                </div>

                <h3 class="text-xs font-bold text-gray-800 dark:text-white leading-tight mb-1 group-hover:text-primary-600 transition line-clamp-2 h-[2.4em]">
                    <?= $row['nama_produk'] ?>
                </h3>

                <div class="mt-auto pt-1 flex items-center justify-between gap-1">
                    <div class="flex flex-col">
                        <span class="text-xs md:text-sm font-bold text-primary-600 dark:text-primary-400">
                            Rp <?= number_format($harga_tampil, 0, ',', '.') ?>
                        </span>
                    </div>
                    
                    <a href="detail.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded text-[10px] font-bold transition">
                        Beli
                    </a>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '<div class="col-span-2 md:col-span-4 lg:col-span-6 text-center py-10 text-gray-500 dark:text-gray-400">
            <p class="text-sm font-bold">Produk tidak ditemukan.</p>
          </div>';
}
?>