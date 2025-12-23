-- Backup Database: db_marhaban 
-- Tanggal: 2025-12-23 10:28:36

-- Tabel: banners 
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO banners VALUES("5","1766110084_I.N.T PARFUM BANER_page-0001.jpg","ayu","","2025-12-19 09:08:04");
INSERT INTO banners VALUES("6","1766217501_BS PARFUME BANER_page-0001.jpg","WWEW","","2025-12-20 14:58:21");

-- Tabel: brands 
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_brand` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO brands VALUES("1","PARFEX");
INSERT INTO brands VALUES("5","MANE");
INSERT INTO brands VALUES("6","IBERCHEM");
INSERT INTO brands VALUES("7","MCBREM");
INSERT INTO brands VALUES("8","TAKASASGO DNN");
INSERT INTO brands VALUES("9","ORIENTAL AROMATIC");
INSERT INTO brands VALUES("10","sds");

-- Tabel: categories 
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `tipe_hitung` enum('volume','qty') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories VALUES("1","Bibit Parfum","volume");
INSERT INTO categories VALUES("2","Botol & Kemasan","qty");

-- Tabel: company_profile 
CREATE TABLE `company_profile` (
  `id` int(11) NOT NULL,
  `nama_toko` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO company_profile VALUES("1","MARHABAN PARFUME","logo.png","tfgh","6281234567890","Jl. Wangi No. 1, Jakarta");

-- Tabel: gallery 
CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO gallery VALUES("1","Program kemitraan","Mitra Marhaban Parfume","galeri_1766201140_139.jpg","2025-12-20 10:25:40");
INSERT INTO gallery VALUES("2","Program kemitraan","Mitra Marhaban Parfume","galeri_1766201174_586.jpg","2025-12-20 10:26:14");
INSERT INTO gallery VALUES("3","gathering","pkerk","galeri_1766201695_498.png","2025-12-20 10:34:55");

-- Tabel: info_toko 
CREATE TABLE `info_toko` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(100) DEFAULT 'Marhaban Parfume',
  `no_wa` varchar(20) DEFAULT '628123456789',
  `alamat` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `link_fb` varchar(255) DEFAULT '',
  `link_ig` varchar(255) DEFAULT '',
  `link_tiktok` varchar(255) DEFAULT '',
  `link_maps` text DEFAULT NULL,
  `header_program` varchar(255) DEFAULT NULL,
  `header_profil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO info_toko VALUES("1","MARHABAN PARFUME ","625711811800","Jl. R. Saleh S. Bustaman No.4, RT.02/RW.11, Empang, Kec. Bogor Sel., Kota Bogor, Jawa Barat 16132"," Bisnis Marhaban Parfume adalah sebuah usaha yang bergerak di bidang distribusi wewangian, berfokus sebagai Pusat Grosir & Eceran Bibit Parfum. Toko ini memposisikan diri sebagai penyedia parfum berkualitas (bibit murni/non-alkohol) dengan harga yang kompetitif.\n\nProduk & Layanan Utama Berdasarkan kategori dan fitur yang ada di website, Marhaban Parfume menawarkan:\n\nBibit Parfum (Murni): Menjual bibit parfum literan atau ukuran khusus dari berbagai brand (seperti Iberchem, Macbrem, dll).\n\nPerlengkapan Parfum: Menjual botol-botol parfum kosong untuk kebutuhan isi ulang.\n\nJasa Refill (Isi Ulang): Layanan isi ulang parfum untuk pengguna harian.\n\nProgram Kemitraan: Memiliki fitur unggulan \"Paket Usaha\", yang menargetkan para pemula atau reseller yang ingin memulai bisnis parfum dari nol dengan panduan dan peralatan lengkap.","logo_1766389133.png","","https://www.instagram.com/marhabanparfum?igsh=MWhzbGRiZno3ZWM3dA==","https://www.tiktok.com/@marhabanparfum?_r=1&_t=ZS-92NDcvRpw4l","   <iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.7078994340877!2d106.79515432026281!3d-6.6063026081829355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c5d18c750557%3A0x8c2366fb253444ed!2sMarhaban%20Parfum!5e0!3m2!1sid!2sid!4v1766215344844!5m2!1sid!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>","header_program_1766480270.jpg","header_profil_1766481963.jpg");

-- Tabel: pricing_rules 
CREATE TABLE `pricing_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `label_ukuran` varchar(50) DEFAULT NULL,
  `pengali` int(11) DEFAULT NULL,
  `margin_persen` decimal(5,2) DEFAULT NULL,
  `is_custom_karton` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `pricing_rules_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pricing_rules VALUES("10","1","100 ML","100","42.09","0");
INSERT INTO pricing_rules VALUES("11","1","500 ML","500","35.03","0");
INSERT INTO pricing_rules VALUES("12","1","1 LITER","1000","30.00","0");
INSERT INTO pricing_rules VALUES("13","1","5 LITER","5000","29.00","0");
INSERT INTO pricing_rules VALUES("14","1","10 LITER","10000","27.00","0");
INSERT INTO pricing_rules VALUES("15","1","25 LITER","25000","20.00","0");
INSERT INTO pricing_rules VALUES("16","2","1 lusin","12","25.06","0");
INSERT INTO pricing_rules VALUES("17","2","10 Lusin","120","20.00","0");
INSERT INTO pricing_rules VALUES("18","2","1 Karton","1200","16.00","0");

-- Tabel: product_variants 
CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `ukuran` varchar(50) DEFAULT NULL,
  `harga_modal` decimal(15,2) DEFAULT 0.00,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO product_variants VALUES("51","17","100 ML","57500.00","81700.00","0");
INSERT INTO product_variants VALUES("52","17","500 ML","287500.00","388200.00","0");
INSERT INTO product_variants VALUES("53","17","1 LITER","575000.00","747500.00","0");
INSERT INTO product_variants VALUES("54","17","5 LITER","2875000.00","3708800.00","0");
INSERT INTO product_variants VALUES("55","17","10 LITER","5750000.00","7302500.00","0");
INSERT INTO product_variants VALUES("56","17","25 LITER","14375000.00","17250000.00","0");
INSERT INTO product_variants VALUES("57","18","100 ML","40600.00","57700.00","0");
INSERT INTO product_variants VALUES("58","18","500 ML","203000.00","274200.00","0");
INSERT INTO product_variants VALUES("59","18","1 LITER","406000.00","527800.00","0");
INSERT INTO product_variants VALUES("60","18","5 LITER","2030000.00","2618700.00","0");
INSERT INTO product_variants VALUES("61","18","10 LITER","4060000.00","5156200.00","0");
INSERT INTO product_variants VALUES("62","18","25 LITER","10150000.00","12180000.00","0");
INSERT INTO product_variants VALUES("63","19","100 ML","45900.00","65300.00","0");
INSERT INTO product_variants VALUES("64","19","500 ML","229500.00","309900.00","0");
INSERT INTO product_variants VALUES("65","19","1 LITER","459000.00","596700.00","0");
INSERT INTO product_variants VALUES("66","19","5 LITER","2295000.00","2960600.00","0");
INSERT INTO product_variants VALUES("67","19","10 LITER","4590000.00","5829300.00","0");
INSERT INTO product_variants VALUES("68","19","25 LITER","11475000.00","13770000.00","0");
INSERT INTO product_variants VALUES("94","26","1 lusin","44400.00","55600.00","0");
INSERT INTO product_variants VALUES("95","26","10 Lusin","444000.00","532800.00","0");
INSERT INTO product_variants VALUES("96","26","1 Karton","888000.00","1030100.00","0");

-- Tabel: products 
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` enum('parfum','botol','bahan') NOT NULL,
  `harga_modal` decimal(10,2) DEFAULT NULL,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `jumlah_klik` int(11) DEFAULT 0,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `hpp_modal` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES("17","FAHRENHEIT","","parfum","","81700.00","0","1766133278_FAHRENHEIT.jpg","0","2025-12-19 15:34:38","0","5","1","575.00");
INSERT INTO products VALUES("18","SUNSET FANTASY","","parfum","","57700.00","0","1766133347_FANTASY SUNSET.jpg","0","2025-12-19 15:35:47","0","5","1","406.00");
INSERT INTO products VALUES("19","GARUDA","","parfum","","65300.00","0","1766133405_GARUDA.jpg","0","2025-12-19 15:36:45","0","7","1","459.00");
INSERT INTO products VALUES("26","BOTOL SCREW 30ML","semi press\n\n1 karton isi 240pcs","parfum","","0.00","0","1766389428_savage 30ml.png","0","2025-12-22 14:43:48","0","1","2","3700.00");

-- Tabel: program_images 
CREATE TABLE `program_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO program_images VALUES("1","2","prog_2_1766206549_0.jpg");
INSERT INTO program_images VALUES("2","2","prog_2_1766206549_1.jpg");
INSERT INTO program_images VALUES("3","2","prog_2_1766206549_2.jpg");
INSERT INTO program_images VALUES("4","2","prog_2_1766206549_3.jpg");
INSERT INTO program_images VALUES("5","2","prog_2_1766206549_4.jpg");
INSERT INTO program_images VALUES("6","2","prog_2_1766206549_5.jpg");
INSERT INTO program_images VALUES("7","2","prog_2_1766206549_6.jpg");
INSERT INTO program_images VALUES("8","2","prog_2_1766206549_7.jpg");
INSERT INTO program_images VALUES("9","2","prog_2_1766206549_8.jpg");
INSERT INTO program_images VALUES("10","2","prog_2_1766206549_9.jpg");
INSERT INTO program_images VALUES("11","2","prog_2_1766206549_10.jpg");
INSERT INTO program_images VALUES("12","2","prog_2_1766206549_11.jpg");
INSERT INTO program_images VALUES("13","2","prog_2_1766206549_12.jpg");
INSERT INTO program_images VALUES("14","2","prog_2_1766206549_13.jpg");
INSERT INTO program_images VALUES("15","2","prog_2_1766206549_14.jpg");
INSERT INTO program_images VALUES("16","2","prog_2_1766206549_15.jpg");
INSERT INTO program_images VALUES("17","2","prog_2_1766206549_16.jpg");
INSERT INTO program_images VALUES("18","2","prog_2_1766206549_17.jpg");
INSERT INTO program_images VALUES("19","2","prog_2_1766206549_18.jpg");
INSERT INTO program_images VALUES("20","2","prog_2_1766206549_19.jpg");

-- Tabel: programs 
CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `no_hp` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO programs VALUES("2","PAKET USAHA","Paket ini cocok banget buat kakaknya yang lagi mau eksplor dunia parfum dari nol. Mulai dari cara meracik parfum, perbandingan aroma, cara cek ketahanan, sampai teknik-teknik dasar lainnya — semuanya bisa kakak pelajari pelan-pelan ya kak.\n\nKita juga sudah sediain buku panduan racik lengkap. Di situ ada cara meracik step-by-step, cara ngitung biaya modal, sampai rekomendasi harga jual biar kakak nggak bingung pas mulai jualan.\n\nPaket ini aman banget buat pemula, baik yang bener-bener mulai dari nol, maupun yang mau buka usaha parfum refill/isi ulang. Semua bahan dan alat yang dibutuhin udah tersedia, jadi kakak bisa langsung praktek tanpa ribet nyari tambahan sana-sini.\n\nGimana kak? Cocok banget buat awal mulai usaha parfum. ?","","2025-12-20 11:55:49","");

-- Tabel: users 
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("2","admin","$2y$10$cDnFsvO0KStlsdomdBmqNuzUFDl1ZSOSiWCIg7M1aQOqjxvzJy0gC","super_admin","2025-12-18 15:18:48");
INSERT INTO users VALUES("3","pegawai","$2y$10$c7c5x.35rUVi4hAJ9LBV0e7a63Nm4Pa4ioKE4tP9jivOVoxYYwMVK","admin","2025-12-18 15:24:17");
INSERT INTO users VALUES("4","agam","$2y$10$vUXb91wDSEyY4bkAbpyZi.6I6fTZtlj0F/L9y582RjctmruP9Fgp2","admin","2025-12-20 13:53:33");

