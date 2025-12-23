<?php
session_start();
include '../config/koneksi.php';
// Fitur Tambah/Edit/Hapus Setting Rule disini (CRUD Sederhana ke tabel pricing_rules)
// Saya buatkan tabel view-nya saja agar ringkas.
$rules = mysqli_query($conn, "SELECT pricing_rules.*, categories.nama_kategori 
                              FROM pricing_rules 
                              JOIN categories ON pricing_rules.category_id = categories.id 
                              ORDER BY categories.nama_kategori, pricing_rules.pengali ASC");
?>
<div class="p-10">
    <h1 class="text-2xl font-bold mb-4">Pengaturan Rumus Harga & Margin</h1>
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-emerald-800 text-white">
                <tr>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Ukuran/Label</th>
                    <th class="p-3">Pengali</th>
                    <th class="p-3">Margin Keuntungan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = mysqli_fetch_assoc($rules)): ?>
                <tr class="border-b">
                    <td class="p-3 font-bold"><?= $r['nama_kategori'] ?></td>
                    <td class="p-3"><?= $r['label_ukuran'] ?></td>
                    <td class="p-3">x<?= $r['pengali'] ?></td>
                    <td class="p-3 text-blue-600 font-bold"><?= $r['margin_persen'] ?>%</td>
                    <td class="p-3">
                        <a href="edit_rule.php?id=<?= $r['id'] ?>" class="text-sm bg-gray-200 px-2 py-1 rounded">Edit</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <p class="mt-4 text-gray-500 text-sm">*Pengali 0 artinya dinamis (seperti 1 Karton isi beda-beda)</p>
</div>