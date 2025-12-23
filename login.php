<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if (password_verify($password, $data['password'])) {
            
            // --- SETTING SESSION (PENTING) ---
            $_SESSION['status_login'] = true; // <--- INI YANG KURANG TADI
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['role']    = $data['role']; // Pastikan di DB kolomnya 'role' (enum: admin, superadmin)
            $_SESSION['nama']    = $data['username'];
            
            echo '<script>window.location="admin/index.php"</script>';
            exit;
        }
    }
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Marhaban Parfume</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#09AFB5', // Warna Tema Baru
                        primaryHover: '#078d91',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96 border-t-4 border-primary">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Login Sistem</h2>
            <p class="text-sm text-gray-500">Silakan masuk untuk mengelola toko</p>
        </div>

        <?php if(isset($error)) echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-sm mb-4 text-center'>$error</div>"; ?>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition" required>
            </div>
            <button type="submit" name="login" class="w-full bg-primary text-white font-bold py-2 px-4 rounded-lg hover:bg-primaryHover transition shadow-lg transform hover:-translate-y-0.5">
                Masuk Sekarang
            </button>
        </form>
        
        <a href="index.php" class="block mt-6 text-center text-sm text-gray-500 hover:text-primary transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Website
        </a>
    </div>
</body>
</html>