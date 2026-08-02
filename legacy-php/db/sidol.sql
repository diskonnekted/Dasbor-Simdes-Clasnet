-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 03 Des 2025 pada 23.33
-- Versi server: 11.8.5-MariaDB-deb13
-- Versi PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sid`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `author` varchar(100) DEFAULT 'Clasnet Group'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `gambar`, `dibuat_pada`, `published`, `author`) VALUES
(2, 'Instalasi IoT Pondokrejo Sleman', 'Desa Pondokrejo, Kecamatan Tempel, Kabupaten Sleman, Yogyakarta, melangkah maju dalam transformasi digital pemerintahan desa. Pemerintah Kalurahan Pondokrejo resmi memasang sistem Internet of Things (IoT) untuk memantau kondisi lingkungan dan penggunaan energi di kantor desa secara real-time.\r\n\r\nPemasangan IoT ini merupakan bagian dari program Smart Village yang digagas oleh Pemerintah Kalurahan bekerja sama dengan Clasnet Group. Sistem IoT yang terpasang mencakup sensor suhu, kelembaban, kualitas udara, penggunaan listrik, serta sistem pengelolaan air dan sampah di area kantor desa.\r\n\r\nKepala Kalurahan Pondokrejo, Bapak Suryanto, mengatakan, “Dengan adanya IoT ini, kami bisa memantau kondisi lingkungan kantor desa secara otomatis dan akurat. Data yang dikumpulkan akan membantu kami dalam pengambilan keputusan, terutama terkait efisiensi energi dan keberlanjutan lingkungan.”\r\n\r\nSensor-sensor tersebut terhubung ke dashboard digital yang dapat diakses melalui perangkat smartphone maupun komputer oleh staf administrasi dan tim teknis desa. Misalnya, jika konsumsi listrik di kantor desa meningkat signifikan di luar jam operasional, sistem akan memberikan notifikasi agar dapat segera ditindaklanjuti.\r\n\r\nSelain itu, data lingkungan seperti kualitas udara dan kelembaban juga dipantau untuk memastikan kenyamanan kerja pegawai dan pengunjung kantor desa, sekaligus sebagai bentuk komitmen terhadap pembangunan berkelanjutan.', 'uploads/1764687389_a4c78ecf-b60e-408a-a24d-3410a82c3748.jpg', '2025-10-29 00:00:00', 1, 'Admin'),
(3, 'Sosialisasi & Pelatihan SID Desa Pekandangan', 'Pada tanggal 2 Oktober 2021, Clasnet bekerja sama dengan DesaPintar menyelenggarakan kegiatan Sosialisasi dan Pelatihan Sistem Informasi Desa (SID) di Desa Pekandangan. Kegiatan ini bertujuan untuk memperkenalkan serta memberikan pemahaman praktis mengenai pemanfaatan teknologi informasi dalam tata kelola pemerintahan desa yang transparan, partisipatif, dan berbasis data.\r\n\r\nAcara diikuti oleh perangkat desa, perwakilan Badan Permusyawaratan Desa (BPD), serta anggota masyarakat yang terlibat dalam pengelolaan administrasi desa. Tim dari Clasnet dan DesaPintar memberikan paparan mengenai konsep SID, manfaatnya bagi pembangunan desa, serta pelatihan langsung penggunaan platform SID berbasis web. Peserta juga diajak untuk mempraktikkan input data kependudukan, profil desa, dan layanan publik melalui antarmuka sistem yang telah disediakan.\r\n\r\nKegiatan berlangsung secara interaktif dan responsif, dengan antusiasme tinggi dari para peserta dalam mengeksplorasi fitur-fitur SID. Harapannya, implementasi SID di Desa Pekandangan dapat memperkuat sistem administrasi desa, meningkatkan pelayanan kepada warga, serta mendukung perencanaan pembangunan yang lebih akurat dan inklusif.\r\n\r\nKolaborasi ini merupakan bagian dari komitmen Clasnet dan DesaPintar dalam mendukung digitalisasi desa dan penguatan kapasitas pemerintah desa melalui teknologi informasi', 'uploads/1764716949_pekandangan.jpg', '2024-06-18 00:00:00', 1, '0'),
(4, 'Pelatihan SID Desa Kesenet', 'Pada tanggal 28 Agustus 2024, Clasnet bersama DesaPintar menyelenggarakan kegiatan Sosialisasi dan Pelatihan Sistem Informasi Desa (SID) di Desa Kesenet. Kegiatan ini dilaksanakan sebagai bagian dari upaya penguatan tata kelola pemerintahan desa berbasis digital, sejalan dengan semangat transparansi, akuntabilitas, dan partisipasi masyarakat dalam pembangunan desa.\r\n\r\nAcara diikuti oleh Kepala Desa Kesenet, perangkat desa, anggota BPD, serta perwakilan masyarakat yang terlibat dalam pengelolaan data dan administrasi desa. Tim fasilitator dari Clasnet dan DesaPintar memberikan paparan mengenai konsep, manfaat, serta implementasi SID dalam mendukung pelayanan publik, perencanaan pembangunan, dan pengambilan keputusan berbasis data.\r\n\r\nDalam sesi pelatihan, peserta diajak untuk mempraktikkan penggunaan platform SID secara langsung, termasuk pengisian data kependudukan, profil desa, aset desa, program kegiatan, serta layanan administrasi online. Pendekatan hands-on memungkinkan peserta memahami alur kerja sistem dan mengeksplorasi fitur-fitur utama seperti dashboard statistik, manajemen pengguna, dan integrasi dengan layanan pemerintahan lainnya.\r\n\r\nKegiatan berlangsung dengan antusias dan partisipatif. Banyak peserta menyampaikan apresiasi atas kemudahan dan manfaat yang ditawarkan SID dalam menyederhanakan administrasi desa dan mempercepat akses informasi publik.\r\n\r\nMelalui kolaborasi ini, Clasnet dan DesaPintar berkomitmen untuk terus mendampingi Desa Kesenet dalam proses adopsi teknologi digital guna mewujudkan desa yang cerdas, informatif, dan responsif terhadap kebutuhan warganya.', 'uploads/1764717492_kesenet2.jpg', '2021-08-18 00:00:00', 1, '0'),
(5, 'Koordinasi Dan Sosialisasi Menuju Kalurahan Digital : Pondokrejo, Sleman, Yogyakarta', 'Dalam rangka mempercepat transformasi digital di tingkat desa, Pemerintah Kalurahan Pondokrejo, Kapanewon Tempel, Kabupaten Sleman, Daerah Istimewa Yogyakarta, sukses menyelenggarakan kegiatan Koordinasi dan Sosialisasi Menuju Kalurahan Digital selama tiga hari berturut-turut, mulai dari 16 hingga 19 Juli 2025. Kegiatan ini merupakan bagian dari komitmen Pemerintah Kalurahan untuk menciptakan tata kelola pemerintahan yang transparan, efisien, serta inklusif melalui pemanfaatan teknologi digital.\r\n\r\nLatar Belakang dan Tujuan\r\nSejalan dengan arahan Pemerintah Kabupaten Sleman dan visi Gubernur DIY tentang Smart Village, Kalurahan Pondokrejo berinisiatif mengembangkan ekosistem digital yang menyentuh seluruh aspek pelayanan publik, pemberdayaan masyarakat, hingga pengelolaan data kependudukan dan potensi lokal. Kegiatan tiga hari ini dirancang untuk menyelaraskan pemahaman antarstakeholder, memperkuat kapasitas sumber daya manusia, serta membangun sinergi antara pemerintah kalurahan, tokoh masyarakat, pemuda, perangkat kalurahan, dan mitra strategis.\r\n\r\nTujuan utama dari kegiatan ini antara lain:\r\n\r\nMembangun kesepahaman bersama mengenai konsep Kalurahan Digital;\r\nMenyusun peta jalan (roadmap) implementasi digitalisasi di Kalurahan Pondokrejo;\r\nMensosialisasikan platform digital yang akan digunakan dalam pelayanan administrasi;\r\nMeningkatkan literasi digital warga, khususnya para perangkat kalurahan dan kader PKK;\r\nMenjaring masukan dari masyarakat terkait kebutuhan digital di lingkungan setempat.\r\nPelaksanaan Kegiatan\r\nHari pertama kegiatan difokuskan pada koordinasi internal antara Lurah, Carik, Kepala Seksi, serta para Dukuh. Diskusi intensif digelar untuk merumuskan standar operasional prosedur (SOP) pelayanan berbasis digital, termasuk penggunaan Sistem Informasi Desa (SID).\r\n\r\nPada hari kedua, dilaksanakan sosialisasi terbuka yang dihadiri oleh lebih dari 150 peserta, termasuk warga, tokoh agama, perwakilan karang taruna, kelompok tani, UMKM, serta mitra strategis seperti Dinas Kominfo Sleman dan komunitas teknologi lokal seperti Jogja Digital Valley. Dalam sesi ini, warga diperkenalkan pada konsep Kalurahan Digital secara utuh, mulai dari layanan administrasi online, transparansi anggaran, hingga pemasaran produk UMKM melalui platform digital.\r\n\r\nHari ketiga menjadi momen puncak dengan simulasi dan pelatihan langsung. Para peserta diajak mempraktikkan penggunaan aplikasi pelayanan kalurahan, termasuk pengajuan surat keterangan secara daring, pelaporan kejadian darurat, dan akses informasi pembangunan.', 'uploads/1764729797_WhatsApp-Image-2025-07-22-at-10.28.30-2048x1153.jpg', '2025-07-20 00:00:00', 1, 'Clasnet Group'),
(6, 'Pelatihan OpenSID dan DESAKTI Se-Kecamatan Punggelan , Dominic Hotel Purwokerto', 'Sebanyak 51 perangkat desa dari 17 desa di Kecamatan Punggelan, Banjarnegara, mengikuti Pelatihan OpenSID dan Aplikasi DESAKTI selama tiga hari, 2–4 Juli 2025, di Hotel Dominic, Purwokerto.\r\n\r\nKegiatan yang bertujuan memperkuat kapasitas desa dalam pengelolaan data digital, pelayanan administrasi, dan transparansi APBDes. Peserta mendapatkan pelatihan praktik langsung tentang input data, integrasi sistem, keamanan informasi, serta pemanfaatan data untuk perencanaan pembangunan.\r\n\r\nCamat Punggelan menekankan bahwa data akurat adalah fondasi pemerintahan desa yang efektif. Usai pelatihan, akan dibentuk komunitas operator SID untuk pendampingan berkelanjutan.\r\n\r\nPelatihan ini menjadi langkah nyata mewujudkan desa digital yang cerdas, terbuka, dan responsif di wilayah Punggelan.', 'uploads/1764736626_DSC01641.jpg', '2025-07-05 00:00:00', 1, 'Clasnet Group'),
(7, 'Sosialisasi SID Desa Karangnangka Pegentan', 'Desa Karangnangka, Kecamatan Peganten, Kabupaten Banjarnegara, menjadi tuan rumah pelaksanaan Sosialisasi Sistem Informasi Desa (SID) yang diikuti tidak hanya oleh perangkat Desa Karangnangka, tetapi juga oleh perwakilan dari beberapa desa tetangga di wilayah Kecamatan Peganten. Kegiatan ini merupakan bagian dari upaya percepatan digitalisasi pemerintahan desa  yang digagas Pemerintah Kabupaten Banjarnegara.\r\n\r\nDalam sosialisasi tersebut, para peserta menerima paparan mengenai manfaat SID dalam mendukung transparansi, akuntabilitas, dan partisipasi publik, serta pelatihan praktis penggunaan platform SID untuk pengelolaan data kependudukan, aset desa, program pembangunan, dan layanan administrasi. Keikutsertaan desa-desa lain menunjukkan komitmen kolektif untuk membangun tata kelola desa yang lebih modern, efisien, dan bebas dari praktik korupsi.\r\n\r\nKegiatan ini juga menegaskan pentingnya kolaborasi antardesa dalam berbagi pengetahuan dan pengalaman, sebagai fondasi menuju desa-desa mandiri digital yang mampu memberikan pelayanan prima kepada masyarakat. Dengan penguatan SID, diharapkan setiap desa di Banjarnegara semakin siap mewujudkan tata kelola pemerintahan yang bersih, terbuka, dan berintegritas.', 'uploads/1764736857_karangnangka.jpg', '2021-09-08 00:00:00', 1, 'Clasnet Group'),
(8, 'Pelatihan OpenSID dan DESAKTI Se-Kecamatan Wanayasa', 'Pemerintah Kecamatan Wanayasa bekerja sama dengan Clasnet Group menyelenggarakan Pelatihan OpenSID dan Aplikasi DESAKTI bagi seluruh desa di wilayahnya. Kegiatan berlangsung selama tiga hari, 8–10 Juli 2025, di Hotel Surya Yudha, Banjarnegara.\r\n\r\nSebanyak 34 perangkat desa dari 17 desa di Kecamatan Wanayasa, termasuk Kepala Desa, Sekretaris Desa, dan operator SID & Desakti, mengikuti pelatihan ini. Materi yang diberikan mencakup pengelolaan data kependudukan, administrasi desa, pelaporan APBDes, integrasi OpenSID dengan DESAKTI, serta praktik pemutakhiran dan backup data.\r\n\r\nPelatihan dilaksanakan secara partisipatif dengan pendekatan simulasi langsung, memastikan peserta mampu mengoperasikan sistem secara mandiri.\r\n\r\nDigitalisasi desa bukan pilihan, tapi kebutuhan. Data yang akurat dan terkini akan mempercepat pelayanan dan perencanaan pembangunan yang tepat sasaran.\r\n\r\nSebagai tindak lanjut, akan dibentuk Komunitas Operator SID Kecamatan Wanayasa untuk memastikan pemanfaatan sistem berjalan berkelanjutan dan terkoordinasi.\r\n\r\nPelatihan di Hotel Surya Yudha ini menjadi fondasi penting menuju desa-desa di Wanayasa yang transparan, efisien, dan siap menghadapi era digital.', 'uploads/1764737051_IMG_20250709_145206.jpg', '2025-07-11 00:00:00', 1, 'Clasnet Group'),
(9, 'Sosialisasi Pemanfaatan SID Pada pendamping Desa', 'Dalam rangka memperkuat tata kelola pemerintahan desa yang akuntabel, transparan, dan bebas dari korupsi, kolusi, dan nepotisme, telah dilaksanakan Sosialisasi Pemanfaatan Sistem Informasi Desa (SID) yang ditujukan khusus bagi Pendamping Desa di wilayah Kabupaten Banjarnegara. \r\nPara pendamping desa—yang memiliki peran penting dalam pembinaan dan pendampingan administrasi desa—diberikan pemahaman mendalam mengenai fungsi SID sebagai media pengelolaan informasi desa yang mendukung perbaikan tata kelola. Materi sosialisasi mencakup pengenalan platform SID, tata cara penginputan data kependudukan dan aset desa, pelaporan program pembangunan, serta integrasi data untuk mendukung pengambilan keputusan berbasis bukti.', 'uploads/1764737143_pendamping.jpg', '2021-09-05 00:00:00', 1, 'Clasnet Group'),
(10, 'Pelatihan Admin Sistem Informasi Desa se-Kecamatan Banjarmangu Tahun 2021', 'Pada tahun 2021, Pemerintah Kecamatan Banjarmangu Kabupaten Banjarnegara menyelenggarakan pelatihan bagi para administrator Sistem Informasi Desa (SID) dari seluruh desa di wilayah kecamatan tersebut. Kegiatan ini bertujuan untuk meningkatkan kapasitas dan kompetensi aparatur desa dalam mengelola sistem informasi berbasis digital, sebagai upaya mendukung transparansi, akuntabilitas, serta pelayanan publik yang lebih efisien di tingkat desa.\r\nMateri pelatihan mencakup pengenalan SID, pengelolaan data kependudukan, input data pembangunan desa, penggunaan aplikasi SID secara praktis, serta cara memanfaatkan data untuk perencanaan pembangunan partisipatif. \r\n\r\nKegiatan ini mendapat apresiasi positif dari para peserta, yang menyatakan bahwa pelatihan sangat membantu dalam memahami pentingnya digitalisasi administrasi desa. Camat Banjarmangu dalam sambutannya menekankan bahwa keberadaan SID bukan hanya sebagai alat administrasi, tetapi juga sebagai sarana pemberdayaan masyarakat melalui akses informasi yang terbuka dan akurat.\r\n\r\nDengan terselenggaranya pelatihan ini, diharapkan seluruh desa di Kecamatan Banjarmangu dapat mengelola SID secara optimal, sehingga mampu mendukung tata kelola pemerintahan desa yang modern, responsif, dan berbasis data.', 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_00.33.40.684.jpg', '2021-07-03 00:00:00', 1, 'Clasnet Group'),
(11, 'Pelatihan dan Koordinasi Sistem Informasi Desa (SID) Kecamatan Punggelan – Part 3', 'Pada hari Selasa, 29 Juli 2025, Pemerintah Kecamatan Punggelan Kabupaten Banjarnegara kembali menggelar kegiatan Pelatihan dan Koordinasi Sistem Informasi Desa (SID) sebagai bagian dari upaya berkelanjutan dalam memperkuat tata kelola pemerintahan desa berbasis digital. Kegiatan ini merupakan seri pertama dari rangkaian pelatihan yang direncanakan sepanjang tahun 2025.', 'uploads/1764738229_IMG_20250729_143319.jpg', '2025-07-30 00:00:00', 1, 'Clasnet Group'),
(12, 'Pelatihan dan Koordinasi Sistem Informasi Desa (SID) Kecamatan Punggelan – Part 1', 'Pemerintah Kecamatan Punggelan menggelar Pelatihan dan Koordinasi Sistem Informasi Desa (SID) seri pertama pada Selasa, 15 Juli 2025, di Balai Desa Klapa. \r\n\r\nMateri mencakup penyegaran penggunaan SID, pemutakhiran data kependudukan, serta praktik langsung input dan pelaporan data. Diskusi aktif muncul terkait kendala teknis di lapangan, seperti jaringan dan perangkat.\r\n\r\nPelatihan ini menjadi langkah awal untuk penguatan tata kelola desa berbasis data digital.', 'uploads/1764738522_Impostor_Gelap20250715_144209______Selebgram_28____________GBC.PORTRAIT.jpg', '2025-07-16 00:00:00', 1, 'Clasnet Group'),
(13, 'Pelatihan dan Koordinasi Sistem Informasi Desa (SID) Kecamatan Punggelan – Part 2', 'Pemerintah Kecamatan Punggelan kembali menggelar lanjutan Pelatihan dan Koordinasi Sistem Informasi Desa (SID) pada Rabu, 23 Juli 2025, di Balai Desa Tanjung Tirta. Kegiatan ini diikuti oleh operator SID dari seluruh desa se-Kecamatan Punggelan.\r\n\r\nFokus pelatihan kali ini adalah pemanfaatan data SID dalam penyusunan Rencana Kerja Pemerintah Desa (RKPDes) dan Anggaran Pendapatan dan Belanja Desa (APBDes), serta monitoring dan evaluasi pembangunan desa berbasis data. Para peserta juga diajak berlatih membuat laporan visualisasi data sederhana untuk mendukung pengambilan keputusan.\r\n\r\nDiskusi berlangsung dinamis, terutama terkait integrasi data SID dengan perencanaan pembangunan yang partisipatif. Camat Punggelan menekankan pentingnya konsistensi dan akurasi data sebagai fondasi tata kelola desa yang transparan dan akuntabel.\r\n\r\nDengan selesainya seri kedua ini, diharapkan SID tidak hanya menjadi arsip digital, tetapi juga menjadi alat strategis dalam pembangunan desa yang berbasis data dan kebutuhan warga.', 'uploads/1764738702_IMG_20250723_142743.jpg', '2025-07-24 00:00:00', 1, 'Clasnet Group'),
(15, 'Sosialisasi Sistem Informasi Desa (SID) di Desa Kandangwangi', 'Pada Senin, 21 Juli 2025, Desa Kandangwangi Kecamatan Wanadadi menggelar sosialisasi Sistem Informasi Desa (SID) yang diikuti oleh perangkat desa dan mahasiswa Kuliah Kerja Nyata (KKN) Universitas setempat. Kegiatan berlangsung di Balai Desa Kandangwangi dan bertujuan untuk memperkenalkan manfaat SID dalam mendukung tata kelola pemerintahan desa yang transparan, efisien, dan berbasis data.', 'uploads/1764742453_IMG_20250721_154358.jpg', '2025-07-22 00:00:00', 1, 'Clasnet Group');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita_foto`
--

CREATE TABLE `berita_foto` (
  `id` int(11) NOT NULL,
  `berita_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita_foto`
--

INSERT INTO `berita_foto` (`id`, `berita_id`, `path`, `caption`, `urutan`, `dibuat_pada`) VALUES
(1, 2, 'uploads/1764687758_344658d2-0544-4344-9659-d092694566ea.jpg', NULL, 1, '2025-12-02 22:02:38'),
(2, 5, 'uploads/1764729797_IMG_20250718_100837.jpg', NULL, 1, '2025-12-03 09:43:17'),
(3, 5, 'uploads/1764729841_IMG_20250717_201912.jpg', NULL, 2, '2025-12-03 09:44:01'),
(4, 5, 'uploads/1764729841_IMG_20250718_100313.jpg', NULL, 3, '2025-12-03 09:44:01'),
(5, 6, 'uploads/1764736626_DSC01644.jpg', NULL, 1, '2025-12-03 11:37:06'),
(6, 6, 'uploads/1764736626_DSC01648.jpg', NULL, 2, '2025-12-03 11:37:06'),
(7, 6, 'uploads/1764736626_DSC01658.jpg', NULL, 3, '2025-12-03 11:37:06'),
(8, 6, 'uploads/1764736626_DSC01683.jpg', NULL, 4, '2025-12-03 11:37:06'),
(9, 8, 'uploads/1764737051_IMG_20250708_194154.jpg', NULL, 1, '2025-12-03 11:44:11'),
(10, 8, 'uploads/1764737051_IMG_20250708_211744.jpg', NULL, 2, '2025-12-03 11:44:11'),
(11, 8, 'uploads/1764737051_IMG_20250709_140512.jpg', NULL, 3, '2025-12-03 11:44:11'),
(12, 8, 'uploads/1764737051_IMG_20250709_195248.jpg', NULL, 4, '2025-12-03 11:44:11'),
(13, 8, 'uploads/1764737051_IMG_20250709_195430.jpg', NULL, 5, '2025-12-03 11:44:11'),
(14, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_00.11.52.186.jpg', NULL, 1, '2025-12-03 11:57:43'),
(15, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_00.38.08.524.jpg', NULL, 2, '2025-12-03 11:57:43'),
(16, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_00.52.44.553.jpg', NULL, 3, '2025-12-03 11:57:43'),
(17, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_01.24.28.618.jpg', NULL, 4, '2025-12-03 11:57:43'),
(18, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_01.32.03.909.jpg', NULL, 5, '2025-12-03 11:57:43'),
(19, 10, 'uploads/1764737863_DOKUMENTASI.mp4_snapshot_01.41.59.000.jpg', NULL, 6, '2025-12-03 11:57:43'),
(20, 11, 'uploads/1764738229_IMG_20250729_141615.jpg', NULL, 1, '2025-12-03 12:03:49'),
(21, 11, 'uploads/1764738229_IMG_20250729_141548.jpg', NULL, 2, '2025-12-03 12:03:49'),
(22, 11, 'uploads/1764738229_IMG_20250729_141440.jpg', NULL, 3, '2025-12-03 12:03:49'),
(23, 12, 'uploads/1764738522_Impostor_Gelap20250715_144438______Selebgram_28____________GBC.PORTRAIT.jpg', NULL, 1, '2025-12-03 12:08:42'),
(24, 12, 'uploads/1764738522_Impostor_Gelap20250715_144227______Selebgram_28____________GBC.PORTRAIT.jpg', NULL, 2, '2025-12-03 12:08:42'),
(25, 12, 'uploads/1764738522_Impostor_Gelap20250715_144052______Selebgram_28____________GBC.PORTRAIT.jpg', NULL, 3, '2025-12-03 12:08:42'),
(26, 13, 'uploads/1764738702_IMG_20250723_143106.jpg', NULL, 1, '2025-12-03 12:11:42'),
(27, 13, 'uploads/1764738702_IMG_20250723_142840.jpg', NULL, 2, '2025-12-03 12:11:42'),
(28, 13, 'uploads/1764738702_IMG_20250723_142745.jpg', NULL, 3, '2025-12-03 12:11:42'),
(29, 13, 'uploads/1764738702_IMG_20250723_142223.jpg', NULL, 4, '2025-12-03 12:11:42'),
(30, 14, 'uploads/1764739275_IMG_20250721_154803.jpg', NULL, 1, '2025-12-03 12:21:15'),
(31, 14, 'uploads/1764739275_IMG_20250721_154752.jpg', NULL, 2, '2025-12-03 12:21:15'),
(32, 14, 'uploads/1764739275_IMG_20250721_154731.jpg', NULL, 3, '2025-12-03 12:21:15'),
(33, 15, 'uploads/1764742453_IMG_20250721_154803.jpg', NULL, 1, '2025-12-03 13:14:13'),
(34, 15, 'uploads/1764742453_IMG_20250721_154752.jpg', NULL, 2, '2025-12-03 13:14:13'),
(35, 15, 'uploads/1764742453_IMG_20250721_154731.jpg', NULL, 3, '2025-12-03 13:14:13'),
(36, 16, 'uploads/1764742465_IMG_20250721_154803.jpg', NULL, 1, '2025-12-03 13:14:25'),
(37, 16, 'uploads/1764742465_IMG_20250721_154752.jpg', NULL, 2, '2025-12-03 13:14:25'),
(38, 16, 'uploads/1764742465_IMG_20250721_154731.jpg', NULL, 3, '2025-12-03 13:14:25'),
(39, 17, 'uploads/1764742472_IMG_20250721_154803.jpg', NULL, 1, '2025-12-03 13:14:32'),
(40, 17, 'uploads/1764742472_IMG_20250721_154752.jpg', NULL, 2, '2025-12-03 13:14:32'),
(41, 17, 'uploads/1764742472_IMG_20250721_154731.jpg', NULL, 3, '2025-12-03 13:14:32'),
(42, 18, 'uploads/1764742491_IMG_20250721_154803.jpg', NULL, 1, '2025-12-03 13:14:51'),
(43, 18, 'uploads/1764742491_IMG_20250721_154752.jpg', NULL, 2, '2025-12-03 13:14:51'),
(44, 18, 'uploads/1764742491_IMG_20250721_154731.jpg', NULL, 3, '2025-12-03 13:14:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `desa`
--

CREATE TABLE `desa` (
  `id` int(11) NOT NULL,
  `nama_kecamatan` varchar(255) NOT NULL,
  `nama_desa` varchar(255) NOT NULL,
  `alamat_website` varchar(512) NOT NULL,
  `jumlah_penduduk` int(11) DEFAULT NULL,
  `website_status` varchar(32) DEFAULT NULL,
  `http_code` int(11) DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `db_penduduk` varchar(32) DEFAULT NULL,
  `sosialisasi` varchar(20) DEFAULT NULL,
  `berita_desa` varchar(20) DEFAULT NULL,
  `developer` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `desa`
--

INSERT INTO `desa` (`id`, `nama_kecamatan`, `nama_desa`, `alamat_website`, `jumlah_penduduk`, `website_status`, `http_code`, `last_checked_at`, `db_penduduk`, `sosialisasi`, `berita_desa`, `developer`) VALUES
(1, 'Kecamatan Bawang', 'Desa Binorong', 'https://binorong-banjarnegara.desa.id', 5295, 'inactive', 0, '2025-12-02 20:28:57', 'BELUM ADA', NULL, NULL, NULL),
(2, 'Kecamatan Bawang', 'Desa Blambangan', 'https://blambangan-banjarnegara.desa.id/', 6066, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(3, 'Kecamatan Bawang', 'Desa Gemuruh', 'https://gemuruh-banjarnegara.desa.id/', 6392, 'inactive', 404, '2025-12-02 20:28:57', 'BELUM ADA', NULL, NULL, NULL),
(4, 'Kecamatan Banjarmangu', 'Desa Banjarkulon', 'https://banjarkulon-banjarnegara.desa.id/', 2349, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(5, 'Kecamatan Banjarmangu', 'Desa Banjarmangu', 'https://banjarmangu-banjarnegara.desa.id/', 3447, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(6, 'Kecamatan Banjarmangu', 'Desa Beji', 'https://beji-banjarnegara.desa.id', 2898, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(7, 'Kecamatan Banjarmangu', 'Desa Gripit', 'https://gripit-banjarnegara.desa.id', 1165, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(8, 'Kecamatan Banjarmangu', 'Desa Jenggawur', 'https://jenggawur-banjarnegara.desa.id/', 2759, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(9, 'Kecamatan Banjarmangu', 'Desa Kalilunjar', 'https://kalilunjar-banjarnegara.desa.id/', 3036, 'inactive', 404, '2025-12-02 20:28:57', 'Sudah Ada', 'belum', 'update', 'clasnet'),
(10, 'Kecamatan Banjarmangu', 'Desa Kendaga', 'https://kendaga-banjarnegara.desa.id', 3888, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'belum', 'tidak update', 'clasnet'),
(11, 'Kecamatan Banjarmangu', 'Desa Kesenet', 'https://kesenet-banjarnegara.desa.id/', 4191, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', '', 'clasnet'),
(12, 'Kecamatan Banjarmangu', 'Desa Majatengah', 'https://majatengah-banjarmangu.sistemdata.id/', 1167, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(13, 'Kecamatan Banjarmangu', 'Desa Paseh', 'https://paseh-banjarnegara.desa.id', 3053, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'lainnya'),
(14, 'Kecamatan Banjarmangu', 'Desa Pekandangan', 'https://pekandangan-banjarnegara.desa.id/', 2380, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(15, 'Kecamatan Banjarmangu', 'Desa Prendengan', 'https://prendengan-banjarmangu.sistemdata.id/', 2360, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(16, 'Kecamatan Banjarmangu', 'Desa Rejasari', 'https://rejasari-banjarnegara.desa.id/', 2384, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(17, 'Kecamatan Banjarmangu', 'Desa Sigeblog', 'https://sigeblog-banjarnegara.desa.id', 4212, 'inactive', 0, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(18, 'Kecamatan Banjarmangu', 'Desa Sijenggung', 'https://sijenggung-banjarnegara.desa.id/', 1892, 'inactive', 404, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'ada', 'clasnet'),
(19, 'Kecamatan Banjarmangu', 'Desa Sijeruk', 'https://sijeruk-banjarnegara.desa.id/', 2372, 'inactive', 404, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(20, 'Kecamatan Banjarmangu', 'Desa Sipedang', 'https://sipedang-banjarnegara.desa.id/', 4005, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(21, 'Kecamatan Madukara', 'Desa Bantarwaru', 'https://bantarwaru-madukara.webdeva.io/', 3645, 'active', 200, '2025-12-02 20:28:57', 'BELUM ADA', NULL, NULL, NULL),
(22, 'Kecamatan Madukara', 'Desa Clapar', 'https://clapar-madukara.webdeva.io/', 2328, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(23, 'Kecamatan Madukara', 'Desa Dawuhan', 'https://dawuhan-madukara.webdeva.io/', 3301, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(24, 'Kecamatan Madukara', 'Desa Gununggiana', 'https://gununggiana-madukara.webdeva.io/', 2577, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(25, 'Kecamatan Madukara', 'Desa Kaliurip', 'https://kaliurip-banjarnegara.desa.id/', 3641, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(26, 'Kecamatan Madukara', 'Desa Karanganyar', 'https://karanganyar-madukara.sistemdata.id', 949, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(27, 'Kecamatan Madukara', 'Desa Limbangan', 'https://limbangan-banjarnegara.desa.id/', 1798, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(28, 'Kecamatan Madukara', 'Desa Madukara', 'https://madukara-banjarnegara.desa.id/', 2441, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'update', 'clasnet'),
(29, 'Kecamatan Madukara', 'Desa Pagelak', 'https://pagelak-madukara.webdeva.io/', 2453, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(30, 'Kecamatan Madukara', 'Desa Pakelen', 'https://pakelen-banjarnegara.desa.id/', 1518, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(31, 'Kecamatan Madukara', 'Desa Penawangan', 'https://penawangan-banjarnegara.desa.id/', 1116, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(32, 'Kecamatan Madukara', 'Desa Petambakan', 'https://petambakan-madukara.webdeva.io/', 3176, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(33, 'Kecamatan Madukara', 'Desa Rakitan', 'https://rakitan-banjarnegara.desa.id', 2863, 'inactive', 0, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(34, 'Kecamatan Madukara', 'Desa Talunamba', 'https://talunamba-madukara.sistemdata.id', 1654, 'inactive', 0, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(35, 'Kecamatan Purwanegara', 'Desa Merden', 'https://merden-banjarnegara.desa.id/', 11935, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(36, 'Kecamatan Purwanegara', 'Desa Mertasari', 'https://mertasari-banjarnegara.desa.id/', 4986, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(37, 'Kecamatan Wanadadi', 'Desa Gumingsir', 'https://gumingsir-wanadadi.desa.id', 1720, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'update', 'clasnet'),
(38, 'Kecamatan Wanadadi', 'Desa Kandangwangi', 'https://kandangwangi-wanadadi.sistemdata.id', 3885, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', 'belum', '', 'clasnet'),
(39, 'Kecamatan Wanadadi', 'Desa Karangjambe', 'https://karangjambe-banjarnegara.desa.id/', 2172, 'inactive', 403, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(40, 'Kecamatan Wanadadi', 'Desa Karangkemiri', 'https://karangkemiri-banjarnegara.desa.id/', 3174, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(41, 'Kecamatan Wanadadi', 'Desa Lemahjaya', 'https://lemahjaya-wanadadi.sistemdata.id/', 6149, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(42, 'Kecamatan Wanadadi', 'Desa Medayu', 'https://medayu-banjarnegara.desa.id/', 3118, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(43, 'Kecamatan Punggelan', 'Desa Badakarya', 'https://badakarya-banjarnegara.desa.id/', 5442, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(44, 'Kecamatan Punggelan', 'Desa Bondolharjo', 'https://bondolharjo-banjarnegara.desa.id/', 6120, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(45, 'Kecamatan Punggelan', 'Desa Danakerta', 'https://danakerta-banjarnegara.desa.id/', 6405, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', '', 'clasnet'),
(46, 'Kecamatan Punggelan', 'Desa Jembangan', 'https://jembangan-banjarnegara.desa.id/', 6520, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(47, 'Kecamatan Punggelan', 'Desa Karangsari', 'https://karangsari-punggelan.desa.id/', 5810, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(48, 'Kecamatan Punggelan', 'Desa Kecepit', 'https://kecepit-banjarnegara.desa.id/', 6231, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(49, 'Kecamatan Punggelan', 'Desa Klapa', 'https://klapa-banjarnegara.desa.id/', 3703, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(50, 'Kecamatan Punggelan', 'Desa Mlaya', 'https://mlaya-banjarnegara.desa.id/', 2684, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(51, 'Kecamatan Punggelan', 'Desa Petuguran', 'https://petuguran-banjarnegara.desa.id/', 7359, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(52, 'Kecamatan Punggelan', 'Desa Punggelan', 'https://punggelan-banjarnegara.desa.id/', 8415, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(53, 'Kecamatan Punggelan', 'Desa Purwasana', 'https://purwasana-banjarnegara.desa.id/', 5141, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(54, 'Kecamatan Punggelan', 'Desa Sambong', 'https://sambong-banjarnegara.desa.id/', 5059, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(55, 'Kecamatan Punggelan', 'Desa Sawangan', 'https://sawangan-banjarnegara.desa.id/', 3594, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(56, 'Kecamatan Punggelan', 'Desa Sidarata', 'https://sidarata-banjarnegara.desa.id/', 4895, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(57, 'Kecamatan Punggelan', 'Desa Tanjungtirta', 'https://tanjungtirta-banjarnegara.desa.id/', 4877, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(58, 'Kecamatan Punggelan', 'Desa Tlaga', 'https://tlaga-banjarnegara.desa.id/', 5375, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(59, 'Kecamatan Punggelan', 'Desa Tribuana', 'https://tribuana-banjarnegara.desa.id/', 4182, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(60, 'Kecamatan Mandiraja', 'Desa Panggisari', 'https://panggisari-banjarnegara.desa.id/', 5321, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', 'sudah', 'update', 'clasnet'),
(61, 'Kecamatan Karangkobar', 'Desa Sampang', 'https://sampang-banjarnegara.desa.id/', 2491, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(62, 'Kecamatan Wanayasa', 'Desa Balun', 'https://balun-banjarnegara.desa.id/', 3931, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(63, 'Kecamatan Wanayasa', 'Desa Bantar', 'https://bantar-banjarnegara.desa.id/', 2608, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(64, 'Kecamatan Wanayasa', 'Desa Dawuhan', 'https://dawuhan-banjarnegara.desa.id/', 2023, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(65, 'Kecamatan Wanayasa', 'Desa Jatilawang', 'https://jatilawang-banjarnegara.desa.id/', 5225, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(66, 'Kecamatan Wanayasa', 'Desa Karangtengah', 'https://karangtengah-banjarnegara.desa.id/', 1873, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(67, 'Kecamatan Wanayasa', 'Desa Kasimpar', 'https://kasimpar-banjarnegara.desa.id/', 1808, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(68, 'Kecamatan Wanayasa', 'Desa Kubang', 'https://kubang-banjarnegara.desa.id/', 4145, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', '', 'clasnet'),
(69, 'Kecamatan Wanayasa', 'Desa Legoksayem', 'https://legoksayem-banjarnegara.desa.id/', 1002, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(70, 'Kecamatan Wanayasa', 'Desa Pagergunung', 'https://pagergunung-banjarnegara.desa.id/', 1974, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(71, 'Kecamatan Wanayasa', 'Desa Pandansari', 'https://pandansari-banjarnegara.desa.id/', 3676, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(72, 'Kecamatan Wanayasa', 'Desa Penanggungan', 'https://penanggungan-banjarnegara.desa.id/', 2472, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(73, 'Kecamatan Wanayasa', 'Desa Pesantren', 'https://pesantren-banjarnegara.desa.id/', 3149, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(74, 'Kecamatan Wanayasa', 'Desa Susukan', 'https://susukan-wanayasa.webdeva.io/', 2536, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(75, 'Kecamatan Wanayasa', 'Desa Suwidak', 'https://suwidak-banjarnegara.desa.id/', 1995, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(76, 'Kecamatan Wanayasa', 'Desa Tempuran', 'https://tempuran-banjarnegara.desa.id/', 2964, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(77, 'Kecamatan Wanayasa', 'Desa Wanaraja', 'https://wanaraja-banjarnegara.desa.id/', 5233, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(78, 'Kecamatan Wanayasa', 'Desa Wanayasa', 'https://wanayasa-banjarnegara.desa.id', 5257, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(79, 'Kecamatan Purwareja Klampok', 'Desa Purwareja', 'https://purwareja.layanandesa.cloud/', 9068, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(80, 'Kecamatan Purwareja Klampok', 'Desa Kecitran', 'https://kecitran.layanandesa.cloud/', 6267, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(81, 'Kecamatan Purwareja Klampok', 'Desa Sirkandi', 'https://sirkandi.layanandesa.cloud/', 7759, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(82, 'Kecamatan Purwareja Klampok', 'Desa Pagak', 'https://pagak.layanandesa.cloud/', 3573, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(83, 'Kecamatan Purwareja Klampok', 'Desa Kalilandak', 'https://kalilandak.layanandesa.cloud/', 3650, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(84, 'Kecamatan Purwareja Klampok', 'Desa Klampok', 'https://klampok.layanandesa.cloud/', 7219, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(85, 'Kecamatan Purwareja Klampok', 'Desa Kalimandi', 'https://kalimandi.layanandesa.cloud/', 6373, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(86, 'Kecamatan Purwareja Klampok', 'Desa Kaliwinasuh', 'https://kaliwinasuh-banjarnegara.desa.id/', 5151, 'inactive', 404, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'tidak update', 'clasnet'),
(87, 'Kecamatan Susukan', 'Desa Piasa wetan', 'https://piasawetan.layanandesa.cloud/', 1322, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(88, 'Kecamatan Susukan', 'Desa Pekikiran', 'https://pekikiran.layanandesa.cloud', 2857, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(89, 'Kecamatan Susukan', 'Desa Brengkok', 'https://brengkok-banjarnegara.desa.id/', 2888, 'inactive', 403, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(90, 'Kecamatan Susukan', 'Desa Panerusan kulon', 'https://panerusankulon-banjarnegara.desa.id/', 2674, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'update', 'clasnet'),
(91, 'Kecamatan Susukan', 'Desa Panerusan wetan', 'https://panerusanwetan.layanandesa.cloud', 2944, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(92, 'Kecamatan Susukan', 'Desa Gumelem kulon', 'https://gumelemkulon.layanandesa.cloud', 10836, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(93, 'Kecamatan Susukan', 'Desa Gumelem wetan', 'https://gumelemwetan.layanandesa.cloud', 10733, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(94, 'Kecamatan Susukan', 'Desa Derik', 'https://derik.layanandesa.cloud', 4264, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(95, 'Kecamatan Susukan', 'Desa Berta', 'https://berta.layanandesa.cloud', 4016, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(96, 'Kecamatan Susukan', 'Desa Karangjati', 'https://karangjati-banjarnegara.desa.id', 4874, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'update', 'clasnet'),
(97, 'Kecamatan Susukan', 'Desa Kedawung', 'https://kedawung-banjarnegara.desa.id', 4434, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'update', 'clasnet'),
(98, 'Kecamatan Susukan', 'Desa Dermasari', 'https://dermasari.layanandesa.cloud', 3011, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(99, 'Kecamatan Susukan', 'Desa Susukan', 'https://susukan.layanandesa.cloud', 4018, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(100, 'Kecamatan Susukan', 'Desa Kemranggon', 'https://kemranggon-banjarnegara.desa.id/', 3565, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(101, 'Kecamatan Susukan', 'Desa Karangsalam', 'https://karangsalam-banjarnegara.desa.id/', 1871, 'inactive', 404, '2025-12-02 20:28:57', 'Belum Ada', '', 'update', 'clasnet'),
(102, 'Kecamatan Pagentan', 'Desa Aribaya', 'https://aribaya-banjarnegara.desa.id/', 2342, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(103, 'Kecamatan Pagentan', 'Desa Babadan', 'https://babadan-banjarnegara.desa.id/', 3675, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(104, 'Kecamatan Pagentan', 'Desa Gumingsir', 'https://gumingsir-banjarnegara.desa.id/', 2151, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(105, 'Kecamatan Pagentan', 'Desa Kalitlaga', 'https://kalitlaga-banjarnegara.desa.id/', 2316, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(106, 'Kecamatan Pagentan', 'Desa Karangnangka', 'https://karangnangka-banjarnegara.desa.id/', 1921, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(107, 'Kecamatan Pagentan', 'Desa Karekan', 'https://karekan-banjarnegara.desa.id/', 2847, 'inactive', 404, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(108, 'Kecamatan Pagentan', 'Desa Kasmaran', 'https://kasmaran-banjarnegara.desa.id', 2158, 'inactive', 404, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(109, 'Kecamatan Pagentan', 'Desa Kayuares', 'https://kayuares-banjarnegara.desa.id/', 1850, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(110, 'Kecamatan Pagentan', 'Desa Larangan', 'https://larangan-banjarnegara.desa.id/', 2038, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(111, 'Kecamatan Pagentan', 'Desa Majasari', 'https://majasari-pagentan.desa.id/', 3236, 'inactive', 503, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(112, 'Kecamatan Pagentan', 'Desa Nagasari', 'https://nagasari-banjarnegara.desa.id/', 1772, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(113, 'Kecamatan Pagentan', 'Desa Plumbungan', 'https://plumbungan-pagentan.sistemdata.id/', 2367, 'active', 200, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(114, 'Kecamatan Pagentan', 'Desa Sokaraja', 'https://sokaraja-banjarnegara.desa.id/', 2382, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'update', 'clasnet'),
(115, 'Kecamatan Pagentan', 'Desa Tegaljeruk', 'https://tegaljeruk-pagentan.sistemdata.id/', 1948, 'inactive', 0, '2025-12-02 20:28:57', 'SUDAH ADA', NULL, NULL, NULL),
(116, 'Kecamatan Pagentan', 'Desa Kalibombong', 'https://kalibombong-banjarnegara.desa.id', NULL, 'inactive', 404, '2025-12-02 20:28:57', '', '', 'update', 'clasnet'),
(117, 'Kecamatan Pagentan', 'Desa Majatengah', 'https://majatengah-banjarnegara.desa.id/', NULL, 'inactive', 403, '2025-12-02 20:28:57', '', '', 'update', 'clasnet'),
(118, 'Kecamatan Pagentan', 'Desa Plorengan', 'https://plorengan-banjarnegara.desa.id', NULL, 'inactive', 403, '2025-12-02 20:28:57', '', '', 'tidak update', 'clasnet'),
(119, 'Kecamatan Pagentan', 'Desa Sembawa', 'https://sembawa-banjarnegara.desa.id/', NULL, 'inactive', 404, '2025-12-02 20:28:57', NULL, NULL, NULL, NULL),
(120, 'Kecamatan Pandanarum', 'Desa Beji', 'https://beji-pandanarum.desa.id', 2759, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(121, 'Kecamatan Pandanarum', 'Desa Lawen', 'https://lawen-banjarnegara.desa.id/', 4801, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(122, 'Kecamatan Pandanarum', 'Desa Pandanarum', 'https://pandanarum-banjarnegara.desa.id/', 3087, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(123, 'Kecamatan Pandanarum', 'Desa Pasegeran', 'https://pasegeran-banjarnegara.desa.id/', 3005, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'update', 'clasnet'),
(124, 'Kecamatan Pandanarum', 'Desa Pingit Lor', 'https://pingitlor-pandanarum.webdeva.io/', 2327, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(125, 'Kecamatan Pandanarum', 'Desa Pringamba', 'https://pringamba-pandanarum.webdeva.io/', 2552, 'active', 200, '2025-12-02 20:28:57', 'Sudah Ada', '', 'tidak update', 'clasnet'),
(126, 'Kecamatan Pandanarum', 'Desa Sinduaji', 'https://sinduaji-pandanarum.webdeva.io/', 1908, 'active', 200, '2025-12-02 20:28:57', 'Belum Ada', '', 'tidak update', 'clasnet'),
(127, 'Kecamatan Pandanarum', 'Desa Sirongge', 'https://sirongge-banjarnegara.desa.id/', 3252, 'inactive', 403, '2025-12-02 20:28:57', 'Sudah Ada', '', 'update', 'clasnet'),
(128, 'Kecamatan Banjarnegara', 'Desa Ampelsari', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(129, 'Kecamatan Banjarnegara', 'Desa Argasoka', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(130, 'Kecamatan Banjarnegara', 'Desa Cendana', 'https://cendana-desa.id/', 0, NULL, NULL, NULL, 'Belum Ada', 'belum', 'tidak ada', 'lainnya'),
(131, 'Kecamatan Banjarnegara', 'Desa Karangtengah', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(132, 'Kecamatan Bawang', 'Desa Bandingan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(133, 'Kecamatan Bawang', 'Desa Bawang', 'https://desabawang.id/', 2084, NULL, NULL, NULL, 'Sudah Ada', '', 'update', 'lainnya'),
(134, 'Kecamatan Bawang', 'Desa Depok', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(135, 'Kecamatan Bawang', 'Desa Joho', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(136, 'Kecamatan Bawang', 'Desa Kebondalem', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(137, 'Kecamatan Bawang', 'Desa Kutayasa', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(138, 'Kecamatan Bawang', 'Desa Majalengka', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(139, 'Kecamatan Bawang', 'Desa Mantrianom', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(140, 'Kecamatan Bawang', 'Desa Masaran', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(141, 'Kecamatan Bawang', 'Desa Pucang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(142, 'Kecamatan Bawang', 'Desa Serang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(143, 'Kecamatan Bawang', 'Desa Wanadri', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(144, 'Kecamatan Bawang', 'Desa Watuurip', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(145, 'Kecamatan Bawang', 'Desa Winong', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(146, 'Kecamatan Bawang', 'Desa Wiramastra', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(147, 'Kecamatan Sigaluh', 'Desa Bandingan', 'https://bandingan-sigaluh.desa.id/', 2083, NULL, NULL, NULL, 'Sudah Ada', '', 'update', 'lainnya'),
(148, 'Kecamatan Sigaluh', 'Desa Bojanegara', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(149, 'Kecamatan Sigaluh', 'Desa Gembongan', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(150, 'Kecamatan Sigaluh', 'Desa Kalibenda', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(151, 'Kecamatan Sigaluh', 'Desa Karangmangu', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(152, 'Kecamatan Sigaluh', 'Desa Kemiri', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(153, 'Kecamatan Sigaluh', 'Desa Panawaren', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(154, 'Kecamatan Sigaluh', 'Desa Prigi', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(155, 'Kecamatan Sigaluh', 'Desa Pringamba', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(156, 'Kecamatan Sigaluh', 'Desa Randegan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(157, 'Kecamatan Sigaluh', 'Desa Sawal', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(158, 'Kecamatan Sigaluh', 'Desa Sigaluh', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(159, 'Kecamatan Sigaluh', 'Desa Singomerto', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(160, 'Kecamatan Sigaluh', 'Desa Tunggoro', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(161, 'Kecamatan Sigaluh', 'Desa Wanacipta', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(162, 'Kecamatan Madukara', 'Desa Blitar', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(163, 'Kecamatan Madukara', 'Desa Kutayasa', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(164, 'Kecamatan Madukara', 'Desa Pekauman', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(165, 'Kecamatan Madukara', 'Desa Sered', '', NULL, NULL, NULL, NULL, 'Sudah Ada', '', '', 'lainnya'),
(166, 'Kecamatan Purwanegara', 'Desa Danaraja', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(167, 'Kecamatan Purwanegara', 'Desa Gumiwang', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(168, 'Kecamatan Purwanegara', 'Desa Kaliajir', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(169, 'Kecamatan Purwanegara', 'Desa Kalipelus', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(170, 'Kecamatan Purwanegara', 'Desa Kalitengah', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(171, 'Kecamatan Purwanegara', 'Desa Karanganyar', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(172, 'Kecamatan Purwanegara', 'Desa Kutawuluh', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(173, 'Kecamatan Purwanegara', 'Desa Parakan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(174, 'Kecamatan Purwanegara', 'Desa Petir', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(175, 'Kecamatan Purwanegara', 'Desa Pucungbedug', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(176, 'Kecamatan Purwanegara', 'Desa Purwanegara', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(177, 'Kecamatan Wanadadi', 'Desa Kasalib', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(178, 'Kecamatan Wanadadi', 'Desa Linggasari', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(179, 'Kecamatan Wanadadi', 'Desa Tapen', 'https://tapen-banjarnegara.desa.id/', 97, NULL, NULL, NULL, 'Belum Ada', '', 'tidak update', 'lainnya'),
(180, 'Kecamatan Wanadadi', 'Desa Wanadadi', 'https://wanadadi.desa.id/', 49, NULL, NULL, NULL, 'Belum Ada', '', 'update', 'lainnya'),
(181, 'Kecamatan Wanadadi', 'Desa Wanakarsa', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(182, 'Kecamatan Mandiraja', 'Desa Mandirajawetan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(183, 'Kecamatan Mandiraja', 'Desa Mandirajakulon', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(184, 'Kecamatan Mandiraja', 'Desa Banjengan', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(185, 'Kecamatan Mandiraja', 'Desa Kebakalan', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(186, 'Kecamatan Mandiraja', 'Desa Kertayasa', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(187, 'Kecamatan Mandiraja', 'Desa Candiwulan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(188, 'Kecamatan Mandiraja', 'Desa Simbang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(189, 'Kecamatan Mandiraja', 'Desa Blimbing', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(190, 'Kecamatan Mandiraja', 'Desa Purwasaba', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(191, 'Kecamatan Mandiraja', 'Desa Glempang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(192, 'Kecamatan Mandiraja', 'Desa Kebanaran', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(193, 'Kecamatan Mandiraja', 'Desa Salamerta', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(194, 'Kecamatan Mandiraja', 'Desa Somawangi', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(195, 'Kecamatan Mandiraja', 'Desa Jalatunda', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(196, 'Kecamatan Mandiraja', 'Desa Kaliwungu', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(197, 'Kecamatan Karangkobar', 'Desa Ambal', '', NULL, NULL, NULL, NULL, 'Sudah Ada', '', '', 'lainnya'),
(198, 'Kecamatan Karangkobar', 'Desa Binangun', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(199, 'Kecamatan Karangkobar', 'Desa Gumelar', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(200, 'Kecamatan Karangkobar', 'Desa Jlegong', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(201, 'Kecamatan Karangkobar', 'Desa Karanggondang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(202, 'Kecamatan Karangkobar', 'Desa Karangkobar', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(203, 'Kecamatan Karangkobar', 'Desa Leksana', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(204, 'Kecamatan Karangkobar', 'Desa Pagerpelah', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(205, 'Kecamatan Karangkobar', 'Desa Pasuruhan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(206, 'Kecamatan Karangkobar', 'Desa Paweden', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(207, 'Kecamatan Karangkobar', 'Desa Purwodadi', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(208, 'Kecamatan Karangkobar', 'Desa Slatri', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(209, 'Kecamatan Rakit', 'Rakit Adipasir', 'https://www.adipasir-banjarnegara.desa.id/', 3, NULL, NULL, NULL, 'Belum Ada', '', 'tidak update', 'lainnya'),
(210, 'Kecamatan Rakit', 'Rakit Badamita', 'https://badamita-banjarnegara.desa.id/index.php/first', 2, NULL, NULL, NULL, 'Belum Ada', 'belum', 'update', 'lainnya'),
(211, 'Kecamatan Rakit', 'Rakit Bandingan', 'https://bandingan.desa.id/', 4411, NULL, NULL, NULL, 'Sudah Ada', '', 'update', 'lainnya'),
(212, 'Kecamatan Rakit', 'Rakit Gelang', 'https://gelang-banjarnegara.desa.id/', 0, NULL, NULL, NULL, 'Belum Ada', 'belum', 'update', 'clasnet'),
(213, 'Kecamatan Rakit', 'Rakit Kincang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(214, 'Kecamatan Rakit', 'Rakit Lengkong', 'https://lengkong-banjarnegara.desa.id/', 6038, NULL, NULL, NULL, 'Sudah Ada', '', 'update', 'lainnya'),
(215, 'Kecamatan Rakit', 'Rakit Luwung', 'https://www.luwung-banjarnegara.desa.id/', NULL, NULL, NULL, NULL, '', '', '', 'lainnya'),
(216, 'Kecamatan Rakit', 'Rakit Pingit', 'https://pingit-banjarnegara.desa.id/', 0, NULL, NULL, NULL, 'Belum Ada', '', 'tidak update', 'lainnya'),
(217, 'Kecamatan Rakit', 'Rakit Rakit', 'https://www.rakit-banjarnegara.desa.id/', 4548, NULL, NULL, NULL, 'Sudah Ada', '', 'tidak update', 'lainnya'),
(218, 'Kecamatan Rakit', 'Rakit Situwangi', 'https://www.situwangi-banjarnegara.desa.id/', 619, NULL, NULL, NULL, 'Sudah Ada', '', 'update', 'lainnya'),
(219, 'Kecamatan Rakit', 'Rakit Tanjunganom', 'https://www.tanjunganom-banjarnegara.desa.id/', 3613, NULL, NULL, NULL, 'Sudah Ada', 'belum', 'update', 'lainnya'),
(220, 'Kecamatan Pejawaran', 'Desa Beji', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(221, 'Kecamatan Pejawaran', 'Desa Biting', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(222, 'Kecamatan Pejawaran', 'Desa Condong Campur', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(223, 'Kecamatan Pejawaran', 'Desa Darmayasa', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(224, 'Kecamatan Pejawaran', 'Desa Gembol', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(225, 'Kecamatan Pejawaran', 'Desa Giritirta', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(226, 'Kecamatan Pejawaran', 'Desa Grogol', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(227, 'Kecamatan Pejawaran', 'Desa Kalilunjar', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(228, 'Kecamatan Pejawaran', 'Desa Karangsari', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(229, 'Kecamatan Pejawaran', 'Desa Panusupan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(230, 'Kecamatan Pejawaran', 'Desa Pegundungan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(231, 'Kecamatan Pejawaran', 'Desa Pejawaran', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(232, 'Kecamatan Pejawaran', 'Desa Ratamba', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(233, 'Kecamatan Pejawaran', 'Desa Sarwodadi', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(234, 'Kecamatan Pejawaran', 'Desa Semangkung', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(235, 'Kecamatan Pejawaran', 'Desa Sidengok', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(236, 'Kecamatan Pejawaran', 'Desa Tlahap', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(237, 'Kecamatan Pagentan', 'Desa Metawana', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(238, 'Kecamatan Pagentan', 'Desa Pagentan', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(239, 'Kecamatan Batur', 'Desa Bakal', '', 0, NULL, NULL, NULL, 'Belum Ada', '', '', ''),
(240, 'Kecamatan Batur', 'Desa Batur', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(241, 'Kecamatan Batur', 'Desa Dieng Kulon', 'https://www.dieng.desa.id/', 4185, NULL, NULL, NULL, 'Sudah Ada', 'belum', 'update', ''),
(242, 'Kecamatan Batur', 'Desa Karangtengah', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(243, 'Kecamatan Batur', 'Desa Kepakisan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(244, 'Kecamatan Batur', 'Desa Pasurenan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(245, 'Kecamatan Batur', 'Desa Pekasiran', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(246, 'Kecamatan Batur', 'Desa Sumberejo', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(247, 'Kecamatan Kalibening', 'Desa Asinan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(248, 'Kecamatan Kalibening', 'Desa Bedana', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(249, 'Kecamatan Kalibening', 'Desa Gununglangit', 'https://gununglangit-banjarnegara.desa.id/', 2985, NULL, NULL, NULL, 'Sudah Ada', 'belum', 'tidak update', 'lainnya'),
(250, 'Kecamatan Kalibening', 'Desa Kalibening', '', NULL, NULL, NULL, NULL, 'SUDAH ADA', NULL, NULL, NULL),
(251, 'Kecamatan Kalibening', 'Desa Kalibombong', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(252, 'Kecamatan Kalibening', 'Desa Kalisat Kidul', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(253, 'Kecamatan Kalibening', 'Desa Karang Anyar', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(254, 'Kecamatan Kalibening', 'Desa Kasinoman', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(255, 'Kecamatan Kalibening', 'Desa Kertasari', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(256, 'Kecamatan Kalibening', 'Desa Majatengah', 'https://majatengah-banjarnegara.desa.id/', 2387, NULL, NULL, NULL, 'Sudah Ada', 'sudah', 'update', 'clasnet'),
(257, 'Kecamatan Kalibening', 'Desa Plorengan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(258, 'Kecamatan Kalibening', 'Desa Sembawa', 'https://sembawa-banjarnegara.desa.id/', 0, NULL, NULL, NULL, '', '', '', 'lainnya'),
(259, 'Kecamatan Kalibening', 'Desa Sidakangen', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(260, 'Kecamatan Kalibening', 'Desa Sikumpul', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(261, 'Kecamatan Kalibening', 'Desa Sirukem', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(262, 'Kecamatan Kalibening', 'Desa Sirukun', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(263, 'Kecamatan Pagedongan', 'Desa Twelagiri', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(264, 'Kecamatan Pagedongan', 'Desa Pagedongan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(265, 'Kecamatan Pagedongan', 'Desa Kebutuhjurang', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(266, 'Kecamatan Pagedongan', 'Desa Kebutuh Duwur', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(267, 'Kecamatan Pagedongan', 'Desa Pesangkalan', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(268, 'Kecamatan Pagedongan', 'Desa Duren', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(269, 'Kecamatan Pagedongan', 'Desa Lebakwangi', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(270, 'Kecamatan Pagedongan', 'Desa Gunungjati', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL),
(271, 'Kecamatan Pagedongan', 'Desa Gentansari', '', NULL, NULL, NULL, NULL, 'BELUM ADA', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sid1_staging`
--

CREATE TABLE `sid1_staging` (
  `row_id` int(11) NOT NULL,
  `no` varchar(32) DEFAULT NULL,
  `nama_kecamatan` varchar(128) DEFAULT NULL,
  `nama_desa` varchar(128) DEFAULT NULL,
  `alamat_website` varchar(255) DEFAULT NULL,
  `mou_clasnet` varchar(64) DEFAULT NULL,
  `server` varchar(64) DEFAULT NULL,
  `jumlah_penduduk` varchar(32) DEFAULT NULL,
  `database_penduduk` varchar(64) DEFAULT NULL,
  `kirim_data` varchar(64) DEFAULT NULL,
  `pengajuan_domain` varchar(64) DEFAULT NULL,
  `fitur_sid` varchar(64) DEFAULT NULL,
  `lama_baru` varchar(64) DEFAULT NULL,
  `versi_sid` varchar(64) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `opendata` varchar(64) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `nama_kecamatan_ff` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `berita_foto`
--
ALTER TABLE `berita_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `berita_id` (`berita_id`);

--
-- Indeks untuk tabel `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_desa_namadesa` (`nama_kecamatan`,`nama_desa`);

--
-- Indeks untuk tabel `sid1_staging`
--
ALTER TABLE `sid1_staging`
  ADD PRIMARY KEY (`row_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `berita_foto`
--
ALTER TABLE `berita_foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `desa`
--
ALTER TABLE `desa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=273;

--
-- AUTO_INCREMENT untuk tabel `sid1_staging`
--
ALTER TABLE `sid1_staging`
  MODIFY `row_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
