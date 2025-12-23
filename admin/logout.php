<?php
session_start();
session_destroy(); // Hancurkan semua sesi
header("Location: ../index.php"); // Lempar balik ke halaman depan
exit;
?>