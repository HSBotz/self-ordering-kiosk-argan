# Dokumentasi Fitur Varian Hot/Ice

Dokumen ini menjelaskan cara penggunaan dan implementasi fitur varian minuman hot/ice di aplikasi Kedai Coffee Kiosk.

## Struktur Database

Fitur varian hot/ice menggunakan kolom `variant_type` di tabel `order_items` dengan format berikut:

```sql
variant_type VARCHAR(10) NULL
```

Nilai yang valid untuk kolom ini adalah:
- `'hot'` - untuk minuman panas
- `'ice'` - untuk minuman dingin
- `NULL` - jika varian tidak dipilih atau produk tidak memiliki varian

## Pengaturan Kategori Produk

Untuk mengaktifkan fitur varian pada kategori tertentu:

1. Buka halaman admin Kategori
2. Edit kategori yang ingin memiliki varian (misalnya "Kopi" atau "Minuman")
3. Aktifkan opsi "Memiliki Varian" (has_variants)
4. Simpan perubahan

Semua produk dalam kategori tersebut akan memiliki opsi pemilihan varian hot/ice.

## Checkout Flow

1. Ketika pelanggan memilih produk dengan varian:
   - Modal pemilihan varian muncul
   - Pelanggan harus memilih salah satu varian (HOT/ICE) sebelum menambahkan ke keranjang

2. Saat checkout, nilai varian disimpan:
   - Nilai varian disimpan dalam format string: `'hot'` atau `'ice'`
   - Data varian dikirim sebagai bagian dari JSON cart_items
   - `KioskController` memvalidasi dan memastikan hanya nilai valid yang disimpan

## Mengatasi Masalah Umum

### 1. Kolom variant_type tidak ada di database

Jalankan SQL berikut untuk menambahkan kolom:

```sql
ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;
```

Atau gunakan script `direct_fix_variants.php` yang telah disediakan.

### 2. Data varian tidak tersimpan dengan benar

Pastikan:
- Model `OrderItem` memiliki kolom `variant_type` dalam array `$fillable`
- Data varian dikirim dalam format string dengan nilai `'hot'` atau `'ice'` (dalam tanda kutip)
- Normalisasi nilai menggunakan `strtolower()` dan `trim()` sebelum disimpan

### 3. Memperbaiki data varian yang sudah ada

Gunakan script `fix_variants_final.php` dengan parameter:

```
http://localhost/kedai-coffee-kiosk/kedai-kiosk/fix_variants_final.php?order_id=XX
```

Untuk memperbaiki pesanan tertentu dengan ID pesanan = XX.

Atau untuk memperbaiki semua pesanan:

```
http://localhost/kedai-coffee-kiosk/kedai-kiosk/fix_variants_final.php?fix_all=1
```

## File Penting

1. **Model:**
   - `OrderItem.php` - Memiliki kolom variant_type dalam fillable

2. **Controllers:**
   - `KioskController.php` - Menangani penyimpanan varian saat checkout
   - `OrderController.php` - Menampilkan data varian di halaman admin

3. **Views:**
   - `admin/orders/show.blade.php` - Menampilkan varian dalam detail pesanan
   - `admin/orders/index.blade.php` - Menampilkan indikator varian di daftar pesanan

4. **JavaScript:**
   - `public/js/kiosk-variant.js` - Menangani pemilihan varian di frontend

5. **Scripts Perbaikan:**
   - `fix_variants_final.php` - Script komprehensif untuk memeriksa dan memperbaiki data varian
   - `direct_fix_variants.php` - Script langsung untuk perbaikan cepat
   - `add_variant_column.sql` - SQL untuk menambahkan kolom variant_type

## Testing

Setelah melakukan perubahan, lakukan pengujian berikut:

1. Pilih produk dengan varian dan verifikasi dapat memilih HOT/ICE
2. Checkout dan pastikan nilai varian tersimpan dengan benar
3. Periksa halaman admin orders untuk memverifikasi varian ditampilkan dengan benar 
