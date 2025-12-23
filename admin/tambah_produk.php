<?php
session_start();
include '../config/koneksi.php';

// Ambil Data Master
$kat_query = mysqli_query($conn, "SELECT * FROM categories");
$brand_query = mysqli_query($conn, "SELECT * FROM brands");

// Ambil Rumus Default
$rules_array = [];
$rules_q = mysqli_query($conn, "SELECT * FROM pricing_rules ORDER BY pengali ASC");
while($r = mysqli_fetch_assoc($rules_q)){
    $rules_array[] = $r;
}
$json_rules = json_encode($rules_array);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Produk Cepat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-lg border-t-4 border-primary">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Input Produk</h2>
            <div class="flex gap-4 items-center">
                <a href="kategori.php" target="_blank" class="text-sm text-primary hover:text-primaryHover font-bold underline">
                    <i class="fas fa-tags mr-1"></i> Kelola Kategori
                </a>
                <div id="loading_indicator" class="hidden text-primary font-bold animate-pulse flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i> Sedang menyimpan...
                </div>
            </div>
        </div>
        
        <form id="formProduk" enctype="multipart/form-data">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nama Produk</label>
                    <input type="text" name="nama" id="nama_produk" class="w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" required placeholder="Contoh: Baccarat Rouge">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Brand</label>
                    <div class="relative">
                        <select name="brand_id" class="w-full border p-2 rounded-lg bg-white appearance-none focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <?php while($b = mysqli_fetch_assoc($brand_query)): ?>
                                <option value="<?= $b['id'] ?>"><?= $b['nama_brand'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-primary/5 p-6 rounded-xl border border-primary/20 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Kategori</label>
                        <div class="flex gap-2">
                            <div class="relative w-full">
                                <select name="category_id" id="category_id" onchange="gantiKategori()" class="w-full border p-2 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary/50">
                                    <option value="">-- Pilih --</option>
                                    <?php while($k = mysqli_fetch_assoc($kat_query)): ?>
                                        <option value="<?= $k['id'] ?>" data-tipe="<?= $k['tipe_hitung'] ?>"><?= $k['nama_kategori'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="button" onclick="bukaModalKategori()" class="bg-primary text-white px-3 rounded-lg hover:bg-primaryHover transition shadow-sm" title="Tambah Kategori">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700" id="label_hpp">HPP Dasar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold">Rp</span>
                            <input type="number" id="hpp_input" name="hpp_dasar" class="w-full border p-2 pl-10 rounded-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/50" placeholder="0" onkeyup="generateVarianAwal()">
                        </div>
                    </div>
                    <div id="box_karton" class="hidden">
                        <label class="block text-sm font-bold mb-1 text-gray-700">Isi per Karton</label>
                        <input type="number" id="isi_karton" name="isi_karton" class="w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" value="100" onkeyup="generateVarianAwal()">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-gray-700 text-lg">Varian Ukuran & Harga</h3>
                    <button type="button" onclick="tambahBarisManual()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow transition transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-1"></i> Tambah Ukuran Custom
                    </button>
                </div>
                
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="p-3 w-1/4 text-left">Ukuran</th>
                                <th class="p-3 w-1/5 text-left">Modal (HPP Total)</th>
                                <th class="p-3 w-1/6 text-left">Margin (%)</th>
                                <th class="p-3 w-1/4 text-left">Harga Jual</th>
                                <th class="p-3 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tabel_varian" class="divide-y divide-gray-100">
                            <tr id="empty_row">
                                <td colspan="5" class="p-8 text-center text-gray-400 bg-gray-50">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-calculator text-3xl opacity-30"></i>
                                        <span>Belum ada settingan otomatis.<br>Silakan pilih Kategori & isi HPP di atas.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Foto Produk</label>
                    <input type="file" name="foto" id="foto_input" class="w-full border p-2 rounded-lg bg-white text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="w-full border p-2 rounded-lg h-24 focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
                </div>
            </div>

            <div class="mb-8 bg-yellow-50 p-4 rounded-xl border border-yellow-200 flex items-start gap-3 shadow-sm">
                <input type="checkbox" name="simpan_aturan" id="simpan_aturan" class="w-5 h-5 text-primary rounded mt-1 cursor-pointer focus:ring-primary">
                <label for="simpan_aturan" class="text-sm text-gray-700 select-none cursor-pointer">
                    <b class="text-gray-900 block mb-1">Simpan Settingan Ukuran ini?</b>
                    <span class="text-gray-500">
                        Jika dicentang, ukuran & margin yang Anda buat sekarang akan disimpan sebagai <b>DEFAULT</b>. 
                        Nanti saat input produk lain di kategori ini, ukuran tersebut akan otomatis muncul.
                    </span>
                </label>
            </div>

            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primaryHover transition shadow-lg text-lg transform hover:-translate-y-1">
                <i class="fas fa-save mr-2"></i> SIMPAN PRODUK
            </button>
        </form>
    </div>

    <div id="modalKategori" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-96 transform transition-all scale-100">
            <h3 class="text-xl font-bold mb-4 text-primary border-b pb-2">Tambah Kategori Baru</h3>
            <form id="formKategoriAjax">
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50" required placeholder="Cth: Parfum Mobil">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Tipe Perhitungan</label>
                    <select name="tipe_hitung" class="w-full border p-2 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="volume">Volume (ML/Liter)</option>
                        <option value="qty">Qty (Pcs/Lusin)</option>
                    </select>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="tutupModalKategori()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg font-bold hover:bg-primaryHover transition shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const dbRules = <?= $json_rules ?>;
        let manualRowsData = [];
        let rowCount = 0;

        function generateVarianAwal(simpanDulu = true) {
            if(simpanDulu) saveManualRowsState(); 

            let catSelect = document.getElementById('category_id');
            if(catSelect.selectedIndex === -1 || catSelect.value === "") return;

            let catId = catSelect.value;
            let hppDasar = parseFloat(document.getElementById('hpp_input').value) || 0;
            let isiKarton = parseFloat(document.getElementById('isi_karton').value) || 1; 
            let tbody = document.getElementById('tabel_varian');
            
            tbody.innerHTML = ''; 

            let activeRules = dbRules.filter(r => r.category_id == catId);
            
            if(activeRules.length > 0) {
                activeRules.forEach((rule) => {
                    let pengali = parseFloat(rule.pengali);
                    if(pengali === 0 || rule.is_custom_karton == 1 || isKartonLabel(rule.label_ukuran)) {
                        pengali = tebakPengali(rule.label_ukuran);
                    }
                    let modalTotal = hppDasar * pengali;
                    tambahBarisTabel(rule.label_ukuran, modalTotal, rule.margin_persen, false, pengali);
                });
            }

            manualRowsData.forEach((row) => {
                let pengaliPakai = row.pengali;
                if(isKartonLabel(row.ukuran)) {
                    pengaliPakai = tebakPengali(row.ukuran); 
                }
                if(pengaliPakai === 0) {
                    pengaliPakai = tebakPengali(row.ukuran);
                }
                let modalPakai = row.modal;
                if(pengaliPakai > 0 && hppDasar > 0) {
                    modalPakai = hppDasar * pengaliPakai;
                }
                tambahBarisTabel(row.ukuran, modalPakai, row.margin, true, pengaliPakai);
            });

            if(tbody.innerHTML === '') {
                 tbody.innerHTML = '<tr id="empty_row"><td colspan="5" class="p-8 text-center text-gray-400 bg-gray-50"><div class="flex flex-col items-center gap-2"><i class="fas fa-calculator text-3xl opacity-30"></i><span>Belum ada settingan otomatis.<br>Silakan pilih Kategori & isi HPP di atas.</span></div></td></tr>';
            }
        }

        function isKartonLabel(text) {
            if(!text) return false;
            let t = text.toLowerCase();
            return t.includes('karton') || t.includes('dus') || t.includes('box') || t.includes('ctn');
        }

        function tebakPengali(teksUkuran) {
            if(!teksUkuran) return 0;
            let text = teksUkuran.toLowerCase();
            let numberMatches = text.match(/[0-9.]+/);
            let qty = numberMatches ? parseFloat(numberMatches[0]) : 1;

            if(text.includes('liter') || text.includes('lt')) return qty * 1000;
            if(text.includes('ml')) return qty;
            if(text.includes('lusin')) return qty * 12;
            if(text.includes('gross')) return qty * 144;
            if(text.includes('kodi'))  return qty * 20;
            
            if(isKartonLabel(text)) {
                let isiPerKarton = parseFloat(document.getElementById('isi_karton').value) || 1;
                return qty * isiPerKarton;
            }
            if(text.includes('satuan') || text.includes('pcs')) return 1;
            return qty;
        }

        function tambahBarisTabel(ukuran, modal, margin, isManual, pengaliAwal = 0) {
            let tbody = document.getElementById('tabel_varian');
            if(document.getElementById('empty_row')) tbody.innerHTML = '';

            let index = rowCount++;
            let hargaJual = hitungJual(modal, margin);
            let bgClass = isManual ? 'bg-blue-50' : 'bg-white';
            let modalReadonly = (pengaliAwal > 0) ? 'readonly' : ''; 
            let modalBg = (pengaliAwal > 0) ? 'bg-gray-100 text-gray-500' : 'bg-white border-blue-300 text-black font-bold';
            let rowClass = isManual ? 'row-manual' : 'row-system';
            
            let row = document.createElement('tr');
            row.className = `${bgClass} border-b hover:bg-gray-100 ${rowClass}`;
            
            row.innerHTML = `
                <td class="p-2">
                    <input type="text" name="varian[${index}][ukuran]" value="${ukuran}" 
                           class="w-full border p-1.5 rounded-md font-bold text-gray-700 input-ukuran focus:ring-2 focus:ring-primary/50 focus:outline-none"
                           placeholder="Cth: 100 ML"
                           oninput="autoUpdateRow(this)"> 
                    <input type="hidden" name="varian[${index}][pengali]" value="${pengaliAwal}" class="input-pengali">
                </td>
                <td class="p-2">
                    <input type="number" name="varian[${index}][modal]" value="${modal}" 
                           class="w-full border p-1.5 rounded-md font-bold input-modal ${modalBg} focus:ring-2 focus:ring-primary/50 focus:outline-none" 
                           ${modalReadonly}
                           oninput="updateHargaRow(this)">
                </td>
                <td class="p-2">
                    <div class="flex items-center">
                        <input type="number" step="0.1" value="${margin}" 
                               class="w-16 border p-1.5 rounded-md font-bold text-blue-600 text-center input-margin focus:ring-2 focus:ring-blue-500/50 focus:outline-none" 
                               oninput="updateHargaRow(this)"> 
                        <span class="ml-1 text-gray-500 font-bold">%</span>
                    </div>
                </td>
                <td class="p-2">
                    <input type="number" name="varian[${index}][harga]" value="${hargaJual}" 
                           class="w-full border p-1.5 rounded-md font-bold text-primary bg-primary/5 input-harga text-lg" readonly>
                </td>
                <td class="p-2 text-center">
                    <button type="button" onclick="hapusBaris(this, ${isManual})" class="text-red-500 w-8 h-8 rounded-full hover:bg-red-100 hover:scale-110 transition flex items-center justify-center font-bold">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        function autoUpdateRow(el) {
            let row = el.closest('tr');
            let ukuranVal = el.value;
            let hppDasar = parseFloat(document.getElementById('hpp_input').value) || 0;
            let pengaliBaru = tebakPengali(ukuranVal);
            row.querySelector('.input-pengali').value = pengaliBaru;

            if(pengaliBaru > 0 && hppDasar > 0) {
                let modalBaru = hppDasar * pengaliBaru;
                let inputModal = row.querySelector('.input-modal');
                inputModal.value = modalBaru;
                inputModal.readOnly = true;
                inputModal.className = "w-full border p-1.5 rounded-md font-bold input-modal bg-gray-100 text-gray-500";
            } else {
                let inputModal = row.querySelector('.input-modal');
                inputModal.readOnly = false;
                inputModal.className = "w-full border p-1.5 rounded-md font-bold input-modal bg-white border-blue-300 text-black";
            }
            updateHargaRow(el);
        }

        function updateHargaRow(el) {
            let row = el.closest('tr');
            let modal = parseFloat(row.querySelector('.input-modal').value) || 0;
            let margin = parseFloat(row.querySelector('.input-margin').value) || 0;
            let harga = hitungJual(modal, margin);
            row.querySelector('.input-harga').value = harga;
            saveManualRowsState();
        }

        function saveManualRowsState() {
            let manualRowsElements = document.querySelectorAll('.row-manual');
            manualRowsData = [];
            manualRowsElements.forEach(row => {
                let ukuran = row.querySelector('.input-ukuran').value;
                let modal = parseFloat(row.querySelector('.input-modal').value) || 0;
                let margin = parseFloat(row.querySelector('.input-margin').value) || 0;
                let pengali = parseFloat(row.querySelector('.input-pengali').value) || 0;
                manualRowsData.push({ ukuran, modal, margin, pengali });
            });
        }

        function triggerSaveManual() { saveManualRowsState(); }

        function tambahBarisManual() {
            manualRowsData.push({ ukuran: "", modal: 0, margin: 30, pengali: 0 });
            generateVarianAwal(false); 
        }

        function hapusBaris(btn, isManual) {
            btn.closest('tr').remove();
            if(isManual) saveManualRowsState();
        }

        function hitungJual(modal, margin) {
            let harga = modal + (modal * (margin / 100));
            return Math.ceil(harga / 100) * 100;
        }

        function gantiKategori() {
            let catSelect = document.getElementById('category_id');
            let selectedOption = catSelect.options[catSelect.selectedIndex];
            if(!selectedOption || !selectedOption.value) return;
            let tipe = selectedOption.getAttribute('data-tipe');
            
            if(tipe === 'volume') {
                document.getElementById('label_hpp').innerText = "HPP per ML";
                document.getElementById('box_karton').classList.add('hidden');
            } else {
                document.getElementById('label_hpp').innerText = "HPP per Pcs";
                document.getElementById('box_karton').classList.remove('hidden');
            }
            generateVarianAwal();
        }

        function bukaModalKategori() { document.getElementById('modalKategori').classList.remove('hidden'); }
        function tutupModalKategori() { document.getElementById('modalKategori').classList.add('hidden'); }
        
        document.getElementById('formKategoriAjax').addEventListener('submit', function(e){
            e.preventDefault();
            let formData = new FormData(this);
            fetch('proses_kategori_ajax.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    tutupModalKategori();
                    this.reset();
                    let select = document.getElementById('category_id');
                    let option = new Option(data.data.nama, data.data.id);
                    option.setAttribute('data-tipe', data.data.tipe);
                    option.selected = true;
                    select.add(option);
                    gantiKategori();
                    Swal.fire('Berhasil', 'Kategori baru ditambahkan', 'success');
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            });
        });

        document.getElementById('formProduk').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            document.getElementById('loading_indicator').classList.remove('hidden');
            
            fetch('proses_produk_baru.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading_indicator').classList.add('hidden');
                if(data.status === 'success') {
                    let pesanJudul = 'Produk Berhasil Disimpan!';
                    let pesanText = 'Settingan default Kategori TIDAK berubah.';
                    let iconType = 'success';

                    if(data.rules_updated === true) {
                        pesanJudul = 'Disimpan & Default Diupdate!';
                        pesanText = 'Settingan ukuran ini telah disimpan sebagai Default baru untuk kategori ini.';
                        iconType = 'info';
                    }

                    Swal.fire({ title: pesanJudul, text: pesanText, icon: iconType, timer: 2000, showConfirmButton: false });
                    setTimeout(() => { location.reload(); }, 2000); 
                } else {
                    Swal.fire('Error', data.message || 'Gagal menyimpan.', 'error');
                }
            })
            .catch(error => {
                document.getElementById('loading_indicator').classList.add('hidden');
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan sistem. Cek Console.', 'error');
            });
        });
    </script>
</body>
</html>