<?php
session_start();
include '../config/koneksi.php';

// CEK KEAMANAN
if (!isset($_SESSION['status_login']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'superadmin')) {
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit;
}

// --- LOGIKA HAPUS DENGAN SESSION NOTIF ---
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    
    // Cegah Hapus Diri Sendiri
    if ($id_hapus == $_SESSION['user_id']) {
        $_SESSION['notif'] = ['type' => 'warning', 'text' => 'Anda tidak bisa menghapus akun sendiri!'];
    } else {
        // Cek Target (Super Admin tidak boleh dihapus sembarangan)
        $cek_target = mysqli_query($conn, "SELECT role FROM users WHERE id='$id_hapus'");
        $d_target = mysqli_fetch_assoc($cek_target);

        if($d_target['role'] == 'super_admin' || $d_target['role'] == 'superadmin'){
             $_SESSION['notif'] = ['type' => 'error', 'text' => 'Tidak dapat menghapus sesama Super Admin!'];
        } else {
            $delete = mysqli_query($conn, "DELETE FROM users WHERE id='$id_hapus'");
            if($delete){
                $_SESSION['notif'] = ['type' => 'success', 'text' => 'User berhasil dihapus!'];
            } else {
                $_SESSION['notif'] = ['type' => 'error', 'text' => 'Gagal menghapus user.'];
            }
        }
    }
    header("Location: users.php");
    exit;
}

// AMBIL DATA USER
$query = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC, username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola User Admin</title>
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
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <?php include 'sidebar_menu.php'; ?>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen User</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola akun staff dan admin toko.</p>
            </div>
            
            <a href="tambah_user.php" class="bg-primary hover:bg-primaryHover text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah Admin Baru
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-primary">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                        <tr>
                            <th class="p-4 border-b w-16 text-center">No</th>
                            <th class="p-4 border-b">Username / Nama</th>
                            <th class="p-4 border-b">Role / Jabatan</th>
                            <th class="p-4 border-b text-center">Status</th>
                            <th class="p-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)): 
                            $is_me = ($row['id'] == $_SESSION['user_id']);
                            $is_super = ($row['role'] == 'super_admin' || $row['role'] == 'superadmin');
                        ?>
                        <tr class="hover:bg-primary/5 transition <?= $is_me ? 'bg-primary/5' : '' ?>">
                            <td class="p-4 text-center font-bold text-gray-500"><?= $no++ ?></td>
                            
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-lg uppercase">
                                        <?= substr($row['username'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-base">
                                            <?= $row['username'] ?>
                                            <?php if($is_me): ?>
                                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full ml-1">Saya</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-gray-400">ID: <?= $row['id'] ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 align-middle">
                                <?php if($is_super): ?>
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-purple-200">
                                        <i class="fas fa-crown mr-1"></i> Super Admin
                                    </span>
                                <?php else: ?>
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold uppercase border border-gray-200">
                                        <i class="fas fa-user-shield mr-1"></i> Admin Staff
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="p-4 text-center align-middle">
                                <span class="text-green-600 font-bold text-xs flex justify-center items-center gap-1">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div> Aktif
                                </span>
                            </td>

                            <td class="p-4 text-center align-middle">
                                <div class="flex justify-center gap-2">
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Ganti Password / Edit">
                                        <i class="fas fa-key"></i>
                                    </a>

                                    <?php if(!$is_me && !$is_super): ?>
                                        <button onclick="hapusUser(<?= $row['id'] ?>)" class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition" title="Hapus User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php else: ?>
                                        <div class="w-8 h-8"></div> 
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Cek Notifikasi PHP
        <?php if (isset($_SESSION['notif'])): ?>
            Toast.fire({
                icon: '<?= $_SESSION['notif']['type'] ?>',
                title: '<?= $_SESSION['notif']['text'] ?>'
            });
            <?php unset($_SESSION['notif']); ?>
        <?php endif; ?>

        // Fungsi Konfirmasi Hapus
        function hapusUser(id) {
            Swal.fire({
                title: 'Yakin hapus user ini?',
                text: "User tidak akan bisa login lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?hapus=" + id;
                }
            })
        }
    </script>

</body>
</html>