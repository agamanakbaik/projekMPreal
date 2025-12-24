<?php
session_start();
include '../config/koneksi.php';

// CEK KEAMANAN
if (!isset($_SESSION['status_login']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'superadmin')) {
    header("Location: index.php"); exit;
}

// AMBIL ID DARI URL
if (!isset($_GET['id'])) {
    header("Location: users.php"); exit;
}
$id = $_GET['id'];
$id_login = $_SESSION['user_id'];

// Cek apakah sedang mengedit diri sendiri?
$is_self_edit = ($id == $id_login);

// AMBIL DATA USER LAMA
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Jika user tidak ditemukan
if (!$data) {
    header("Location: users.php"); exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $username      = mysqli_real_escape_string($conn, $_POST['username']);
    $password_baru = $_POST['password_baru'];
    
    // Variabel error penanda
    $error = null;

    // 1. Cek Username Unik (jika diganti)
    if ($username != $data['username']) {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah digunakan user lain!";
        }
    }

    // 2. Logika Ganti Password
    if (!$error) {
        // Jika Password Baru Diisi
        if (!empty($password_baru)) {
            
            // A. Jika Edit Diri Sendiri -> WAJIB Cek Password Lama
            if ($is_self_edit) {
                $password_lama = $_POST['password_lama'];
                
                if (empty($password_lama)) {
                    $error = "Gagal! Password Lama wajib diisi untuk konfirmasi perubahan.";
                } elseif (!password_verify($password_lama, $data['password'])) {
                    $error = "Password Lama SALAH! Perubahan ditolak.";
                }
            }

            // Jika tidak ada error verifikasi, hash password baru
            if (!$error) {
                $hashed_pass = password_hash($password_baru, PASSWORD_DEFAULT);
                $q_update = "UPDATE users SET username='$username', password='$hashed_pass' WHERE id='$id'";
            }

        } else {
            // Jika Password Baru Kosong -> Cuma update username
            $q_update = "UPDATE users SET username='$username' WHERE id='$id'";
        }

        // Eksekusi Update jika tidak ada error
        if (!$error) {
            if(mysqli_query($conn, $q_update)){
                $_SESSION['notif'] = ['type' => 'success', 'text' => 'Data User Berhasil Diupdate!'];
                header("Location: users.php");
                exit;
            } else {
                $error = "Terjadi kesalahan database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit User</title>
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
    
    <div class="bg-white p-8 rounded-xl shadow-lg w-96 border-t-4 border-yellow-500">
        
        <h2 id="judulRahasia" class="text-2xl font-bold mb-1 text-gray-800 select-none cursor-default" onclick="hitungKlik()">
            Edit User
        </h2>

        <p class="text-sm text-gray-500 mb-6">
            <?php if($is_self_edit): ?>
                Mengubah profil <b>Anda sendiri</b>.
            <?php else: ?>
                Mengubah akun: <b><?= $data['username'] ?></b>
            <?php endif; ?>
        </p>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" value="<?= $data['username'] ?>" class="w-full border pl-10 pr-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
                </div>
            </div>

            <hr class="my-4 border-gray-200">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" name="password_baru" class="w-full border pl-10 pr-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Kosongkan jika tidak diganti">
                </div>
            </div>

            <?php if($is_self_edit): ?>
                <div id="boxPasswordLama" class="hidden mb-6 bg-yellow-50 p-3 rounded-lg border border-yellow-200 animate-pulse">
                    <label class="block text-yellow-800 text-sm font-bold mb-2">
                        <i class="fas fa-shield-alt mr-1"></i> Password Lama
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-yellow-600">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_lama" class="w-full border border-yellow-300 pl-10 pr-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" placeholder="Masukkan password lama">
                    </div>
                    <p class="text-[10px] text-yellow-700 mt-1">*Demi keamanan, verifikasi password lama diperlukan.</p>
                </div>
            <?php endif; ?>

            <div class="flex gap-3 mt-6">
                <a href="users" class="flex-1 bg-gray-100 text-gray-600 py-2 rounded-lg text-center font-bold hover:bg-gray-200 transition">Batal</a>
                <button type="submit" name="update" class="flex-1 bg-yellow-500 text-white py-2 rounded-lg font-bold hover:bg-yellow-600 transition shadow-md">Update</button>
            </div>
        </form>
    </div>

    <script>
        <?php if(isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $error ?>',
                confirmButtonColor: '#eab308'
            });
        <?php endif; ?>

        // --- LOGIKA RAHASIA TANPA JEJAK ---
        let hitungan = 0;
        const targetKlik = 20; 
        const boxPass = document.getElementById('boxPasswordLama');

        function hitungKlik() {
            // Cek jika box masih tersembunyi
            if(boxPass && boxPass.classList.contains('hidden')) {
                hitungan++;
                
                // Tidak ada console.log atau visual counter agar tidak ketahuan

                // Jika mencapai target
                if(hitungan >= targetKlik) {
                    // Munculkan kolom
                    boxPass.classList.remove('hidden'); 
                    
                    // Matikan animasi kedip setelah 1 detik
                    setTimeout(() => {
                        boxPass.classList.remove('animate-pulse');
                    }, 1000);
                    
                    // Notif kecil (Toast) di pojok kanan atas
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Mode Keamanan Dibuka'
                    });
                }
            }
        }
    </script>

</body>
</html>