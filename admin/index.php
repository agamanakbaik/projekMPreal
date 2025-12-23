<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) { 
    header("Location: ../login.php"); 
    exit; 
}

// Cek Role (untuk logika tampilan card)
$is_superadmin = (isset($_SESSION['role']) && ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'super_admin'));

// 1. Ambil Total Produk
$total_produk = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM products"));

// 2. Ambil Total Admin (Hanya hitung jika Super Admin)
$total_admin = 0;
if ($is_superadmin) {
    $total_admin = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users"));
}

// 3. Cek Produk Bermasalah (DIPERBAIKI)
// Logic: Hitung produk yang harganya 0, TAPI abaikan jika produk tersebut punya varian.
// Jadi Botol yang harganya 0 (karena pakai varian) tidak akan dianggap error.
$query_cek_nol = "SELECT COUNT(*) as total 
                  FROM products p
                  WHERE (p.harga_jual = 0 OR p.hpp_modal = 0) 
                  AND (SELECT COUNT(*) FROM product_variants v WHERE v.product_id = p.id) = 0";

$cek_nol = mysqli_query($conn, $query_cek_nol);
$data_nol = mysqli_fetch_assoc($cek_nol);
$total_nol = $data_nol['total'];

// 4. Statistik Klik WA
$q_klik = mysqli_query($conn, "SELECT SUM(jumlah_klik) as total_klik FROM products");
$d_klik = mysqli_fetch_assoc($q_klik);
$total_interaksi = $d_klik['total_klik'] ?? 0;

// 5. Data Grafik (Top 5)
$query_grafik = mysqli_query($conn, "SELECT nama_produk, jumlah_klik FROM products ORDER BY jumlah_klik DESC LIMIT 5");
$labels = [];
$data_klik = [];
while($row = mysqli_fetch_assoc($query_grafik)){
    $labels[] = substr($row['nama_produk'], 0, 15) . '...'; 
    $data_klik[] = $row['jumlah_klik']; 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#09AFB5', primaryHover: '#078d91' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen font-sans">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Ringkasan</h1>
            
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 hidden md:block">Halo, <b><?= $_SESSION['nama'] ?></b></span>
                <!-- <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold shadow-md"> -->
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-primary hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-bold uppercase">Total Produk</h3>
                        <p class="text-3xl mt-2 font-bold text-gray-800"><?= $total_produk ?></p>
                    </div>
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-bold uppercase">Interaksi WA</h3>
                        <p class="text-3xl mt-2 font-bold text-gray-800"><?= $total_interaksi ?></p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Total klik tombol beli</p>
            </div>

            <?php if ($is_superadmin): ?>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-bold uppercase">Admin / Staff</h3>
                        <p class="text-3xl mt-2 font-bold text-gray-800"><?= $total_admin ?></p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 <?= $total_nol > 0 ? 'border-red-500' : 'border-green-500' ?> hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm font-bold uppercase">Data Belum Lengkap</h3>
                        <p class="text-3xl mt-2 font-bold <?= $total_nol > 0 ? 'text-red-600' : 'text-green-600' ?>">
                            <?= $total_nol ?>
                        </p>
                    </div>
                    <div class="p-2 <?= $total_nol > 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' ?> rounded-lg">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Produk tanpa harga & tanpa varian</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">
                <i class="fas fa-chart-bar text-primary mr-2"></i> 5 Produk Terpopuler
            </h3>
            <div class="h-80 w-full">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Jumlah Klik',
                    data: <?= json_encode($data_klik) ?>,
                    backgroundColor: [
                        'rgba(9, 175, 181, 0.8)', // Primary
                        'rgba(7, 141, 145, 0.8)',
                        'rgba(5, 108, 112, 0.8)',
                        'rgba(14, 116, 144, 0.8)',
                        'rgba(8, 145, 178, 0.8)'
                    ],
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { display: true, borderDash: [2, 2] } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>