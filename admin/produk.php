<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login.php");
    exit;
}

// 1. QUERY PRODUK
$query_sql = "SELECT p.*, 
                     b.nama_brand, 
                     c.nama_kategori,
                     (SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) as total_varian,
                     (SELECT MIN(harga_jual) FROM product_variants WHERE product_id = p.id) as harga_terendah_varian
              FROM products p
              LEFT JOIN brands b ON p.brand_id = b.id
              LEFT JOIN categories c ON p.category_id = c.id
              ORDER BY p.id DESC";

$query = mysqli_query($conn, $query_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Produk - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#09AFB5',
                        primaryHover: '#078d91',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Produk</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola katalog, harga, dan varian.</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                    <input type="text" id="searchInput" onkeyup="cariTabel()" placeholder="Cari nama produk..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none transition shadow-sm">
                </div>

                <a href="tambah_produk" class="bg-primary hover:bg-primaryHover text-white px-4 py-2 rounded-lg font-bold shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-primary">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tabelProduk">
                    <thead class="bg-gray-800 text-white uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="p-4 w-16">Foto</th>
                            <th class="p-4">Produk</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Harga Display</th>
                            <th class="p-4 text-center">Varian</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <?php if(mysqli_num_rows($query) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query)): 
                                $harga_final = ($row['harga_jual'] > 0) ? $row['harga_jual'] : $row['harga_terendah_varian'];
                                $hpp_tampil = ($row['hpp_modal'] > 0) ? 'Rp '.number_format($row['hpp_modal'], 0, ',', '.') : '<span class="text-red-500">Belum Set</span>';
                            ?>
                            <tr class="hover:bg-primary/5 transition item-row">
                                <td class="p-4 align-middle">
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden border border-gray-300">
                                        <?php if(!empty($row['foto'])): ?>
                                            <img src="../assets/img/<?= $row['foto'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="flex items-center justify-center h-full text-gray-400"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">
                                            <?= $row['nama_brand'] ?? 'No Brand' ?>
                                        </span>
                                        <span class="font-bold text-gray-800 text-base nama-produk">
                                            <?= $row['nama_produk'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold border border-primary/20">
                                        <?= $row['nama_kategori'] ?? 'Umum' ?>
                                    </span>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-primary text-base">
                                            Rp <?= number_format($harga_final, 0, ',', '.') ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            HPP: <?= $hpp_tampil ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                                        <?= $row['total_varian'] ?> Ukuran
                                    </span>
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="edit_produk?id=<?= $row['id'] ?>" class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= addslashes($row['nama_produk']) ?>')" 
                                                class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded hover:bg-red-600 hover:text-white transition" 
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-10 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                        <p>Belum ada produk yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function cariTabel() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("tabelProduk");
            let tr = table.getElementsByClassName("item-row");

            for (let i = 0; i < tr.length; i++) {
                let td = tr[i].getElementsByClassName("nama-produk")[0];
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        // 3. FUNGSI KONFIRMASI HAPUS SWEETALERT
        function konfirmasiHapus(id, namaProduk) {
            Swal.fire({
                title: 'Yakin hapus produk ini?',
                text: "Produk \"" + namaProduk + "\" dan semua variannya akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik Ya, arahkan ke file hapus.php
                    window.location.href = "hapus?id=" + id;
                }
            })
        }
        
        // Notifikasi Sukses Hapus (Optional: jika ada session notif)
        // Anda bisa menambahkan logika PHP session di sini nanti
    </script>
</body>
</html>