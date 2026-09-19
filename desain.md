# Desain Arsitektur: Product Information System

Dokumen ini berisi rancangan arsitektur untuk sistem manajemen data informasi produk. Pendekatan utama yang digunakan dalam pengembangan arsitektur ini adalah *Separation of Concerns* (SoC). Pola ini diterapkan untuk memisahkan abstraksi data, logika bisnis, dan antarmuka pengguna, sehingga *codebase* menjadi lebih modular, mudah dipelihara (*maintainable*), dan siap untuk *scale-up* (misalnya untuk kebutuhan migrasi ke database RDBMS di masa mendatang).

Sistem ini dikelompokkan ke dalam tiga layer arsitektur utama:

### 1. Data Layer (Data Source / Model)
Layer ini berfungsi sebagai *single source of truth* untuk seluruh data komoditas produk. Mengingat sistem ini dirancang tanpa dependensi eksternal, layer ini diimplementasikan menggunakan struktur *multidimensional array* yang diisolasi di dalam modul `products.php`. 

Atribut entitas data yang dikelola untuk setiap komoditas meliputi:
- `id` (Identifier unik produk)
- `nama` (Nama atau merek produk)
- `kategori` (Klasifikasi jenis produk)
- `harga` (Nilai jual produk)
- `stok` (Kuantitas ketersediaan di gudang)
- `deskripsi` (Spesifikasi atau keterangan detail)

### 2. Processing Layer (Business Logic)
Layer ini bertindak sebagai pusat pemrosesan operasional aplikasi. Seluruh aturan bisnis (*business rules*) dipisahkan ke dalam modul `functions.php` agar tidak mengotori struktur antarmuka UI.

Proses utama yang ditangani pada layer ini mencakup:
- **Kalkulasi Valuasi Aset:** Implementasi fungsi `hitungTotalNilaiStok()` untuk mengalkulasi agregat total nilai aset berdasarkan perkalian antara kuantitas stok dan harga masing-masing produk.
- **Stock Monitoring:** Evaluasi status persediaan barang secara adaptif. Sistem menerapkan *conditional logic* (if-else) untuk mendeteksi anomali ketersediaan barang (stok < 3). Output dari logika ini diekspos sebagai *flag* indikator yang nantinya digunakan oleh layer presentasi untuk memberikan peringatan visual (*critical stock warning*).

### 3. Presentation Layer (View / UI)
Layer ini berfokus pada pengalaman pengguna dan bertindak sebagai *entry point* dari aplikasi. Modul `index.php` merajut (*wiring*) seluruh ekosistem aplikasi dari layer data dan pemrosesan.

Mekanisme kerja layer ini meliputi:
- Mengimpor dependensi state (data) dan fungsi bisnis menggunakan `require_once` untuk meminimalisir *overhead* pemanggilan berkas.
- Melakukan *rendering* data secara dinamis. Data yang ditarik dari *Data Layer* diiterasi menggunakan perulangan `foreach` untuk memproduksi baris tabel HTML secara otomatis.
- Menampilkan *dynamic styling* pada antarmuka. Indikator baris akan berubah warna secara otomatis menyesuaikan *state* dari stok yang diproses pada *Processing Layer*.
