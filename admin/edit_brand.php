<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM brands WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan, kembalikan
if (!$data) {
    header("Location: brands.php");
    exit;
}

// --- PROSES UPDATE ---
if (isset($_POST['update'])) {
    $nama_brand = mysqli_real_escape_string($conn, $_POST['nama_brand']);
    
    // Cek Duplikat (Kecuali punya sendiri)
    $cek = mysqli_query($conn, "SELECT id FROM brands WHERE nama_brand = '$nama_brand' AND id != '$id'");
    
    if (mysqli_num_rows($cek) > 0) {
        $error = "Nama Brand sudah digunakan!";
    } else {
        $update = mysqli_query($conn, "UPDATE brands SET nama_brand='$nama_brand' WHERE id='$id'");
        
        if ($update) {
            // Set notifikasi sukses untuk ditampilkan di halaman brands.php
            $_SESSION['notif'] = ['type' => 'success', 'text' => 'Brand berhasil diperbarui!'];
            header("Location: brands.php");
            exit;
        } else {
            $error = "Gagal mengupdate data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Brand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
<body class="bg-gray-100 flex items-center justify-center h-screen font-sans">
    
    <div class="bg-white p-8 rounded-xl shadow-lg w-96 border-t-4 border-primary">
        <h2 class="text-2xl font-bold mb-1 text-gray-800">Edit Brand</h2>
        <p class="text-sm text-gray-500 mb-6">Ubah nama merek produk.</p>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Brand</label>
                <input type="text" name="nama_brand" value="<?= $data['nama_brand'] ?>" 
                       class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" required>
            </div>
            
            <div class="flex gap-3">
                <a href="brands.php" class="flex-1 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-lg text-center hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" name="update" class="flex-1 bg-primary hover:bg-primaryHover text-white font-bold py-2.5 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                    Update
                </button>
            </div>
        </form>
    </div>

</body>
</html>