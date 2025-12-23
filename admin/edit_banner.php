<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM banners WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update
if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $foto_baru = time() . "_" . $foto;
        
        if(file_exists("../assets/img/" . $data['gambar'])){
            unlink("../assets/img/" . $data['gambar']);
        }
        move_uploaded_file($tmp, "../assets/img/" . $foto_baru);
        $sql = "UPDATE banners SET judul='$judul', gambar='$foto_baru' WHERE id='$id'";
    } else {
        $sql = "UPDATE banners SET judul='$judul' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Banner Berhasil Diupdate!'); window.location='banner.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Banner</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded shadow-lg w-full max-w-lg border-t-4 border-blue-600">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Banner Promo</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Judul Promo</label>
                <input type="text" name="judul" value="<?= $data['judul'] ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Gambar Saat Ini</label>
                <img src="../assets/img/<?= $data['gambar'] ?>" class="w-full h-32 object-cover rounded border">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Ganti Gambar (Opsional)</label>
                <input type="file" name="foto" class="w-full border p-2 rounded bg-gray-50 text-sm">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" name="update" class="flex-1 bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">Simpan</button>
                <a href="banner.php" class="flex-1 bg-gray-300 text-gray-700 font-bold py-2 rounded text-center hover:bg-gray-400">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>