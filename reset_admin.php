<?php
include 'config/kone';

// Kita buat password baru: "admin" (biar gampang)
$password_baru = "admin"; 
$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Hapus user 'admin' yang lama jika ada (biar tidak duplikat)
mysqli_query($conn, "DELETE FROM users WHERE username='admin'");

// Masukkan user baru
$query = "INSERT INTO users (username, password, role) 
          VALUES ('admin', '$password_hash', 'super_admin')";

if (mysqli_query($conn, $query)) {
    echo "<h1>SUKSES!</h1>";
    echo "<p>User berhasil dibuat/direset.</p>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Password: <b>admin</b></p>";
    echo "<br><a href='login'>Klik disini untuk Login</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>