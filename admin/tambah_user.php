<?php
session_start();
include '../config/koneksi.php';

// CEK KEAMANAN
if (!isset($_SESSION['status_login']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: index.php"); exit;
}

// PROSES SIMPAN
if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = 'admin'; // Default Admin Biasa

    // Cek Username
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_pass', '$role')";
        
        if(mysqli_query($conn, $query)){
            // Jika sukses, buat session notif dan redirect ke users.php
            $_SESSION['notif'] = ['type' => 'success', 'text' => 'Admin Baru Berhasil Ditambahkan!'];
            header("Location: users.php");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Admin Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#09AFB5', primaryHover: '#078d91' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    
    <div class="bg-white p-8 rounded-xl shadow-lg w-96 border-t-4 border-primary">
        <h2 class="text-2xl font-bold mb-1 text-gray-800">Tambah Admin</h2>
        <p class="text-sm text-gray-500 mb-6">User ini hanya bisa mengelola produk.</p>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="w-full border pl-10 pr-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" placeholder="Username baru" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="w-full border pl-10 pr-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" placeholder="Password kuat" required>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="users.php" class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-lg text-center font-bold hover:bg-gray-200 transition">Batal</a>
                <button type="submit" name="simpan" class="flex-1 bg-primary text-white py-2 rounded-lg font-bold hover:bg-primaryHover transition shadow-md">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        <?php if(isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $error ?>',
                confirmButtonColor: '#09AFB5'
            });
        <?php endif; ?>
    </script>

</body>
</html>