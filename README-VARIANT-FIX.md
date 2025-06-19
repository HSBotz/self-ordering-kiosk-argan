# Panduan Perbaikan Varian Hot/Ice

## Masalah yang Diperbaiki

1. Kolom `variant_type` belum ada di tabel `order_items` di database
2. Nilai varian tidak disimpan dengan benar saat checkout
3. Data varian tidak ditampilkan di halaman admin orders

## Langkah-langkah Perbaikan

### 1. Jalankan Script Perbaikan

Buka salah satu script berikut di browser untuk memperbaiki masalah:

- Script komprehensif untuk memeriksa dan memperbaiki:
  ```
  http://localhost/kedai-coffee-kiosk/kedai-kiosk/fix_variants_final.php?order_id=31
  ```

- Script perbaikan langsung untuk pesanan #31:
  ```
  http://localhost/kedai-coffee-kiosk/kedai-kiosk/direct_fix_variants.php
  ```

### 2. Perubahan yang Dilakukan

1. **Struktur Database:**
   - Penambahan kolom `variant_type VARCHAR(10) NULL` ke tabel `order_items`

2. **Perbaikan Tampilan Admin:**
   - Pembaruan template `admin/orders/show.blade.php` untuk menampilkan varian dengan jelas
   - Penambahan indikator varian di halaman `admin/orders/index.blade.php`

3. **Pencegahan Masalah Serupa:**
   - Perbaikan pada `KioskController.php` untuk validasi dan sanitasi nilai varian
   - Penambahan script `kiosk-variant.js` untuk menangani pemilihan varian di frontend
   - Pemastian model `OrderItem.php` sudah memiliki kolom `variant_type` dalam fillable

### 3. Cara Menjalankan Script Perbaikan

#### Menambahkan kolom di database:
```sql
ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;
```

#### Memperbaiki data varian untuk pesanan tertentu:
```
fix_variants_final.php?order_id=31
```

#### Memperbaiki semua data varian:
```
fix_variants_final.php?fix_all=1
```

#### Memperbaiki pesanan tertentu dengan varian spesifik:
```
fix_variants_final.php?order_id=31&hot=1,3,5&ice=2,4,6
```
Parameter hot dan ice berisi ID item yang akan diupdate sebagai varian panas atau dingin.

### 4. Melihat Hasil Perbaikan

Setelah menjalankan script perbaikan, buka halaman detail pesanan untuk melihat hasilnya:
```
http://localhost:8000/admin/orders/31
```

Varian hot/ice sekarang akan ditampilkan dengan benar di kolom Varian dengan ikon dan warna yang sesuai:
- 🔥 Panas: Tampil dengan badge merah dan ikon api
- ❄️ Dingin: Tampil dengan badge biru dan ikon salju

## File yang Diperbarui

1. `kedai-kiosk/app/Http/Controllers/KioskController.php`
   - Perbaikan validasi nilai variant_type

2. `kedai-kiosk/resources/views/admin/orders/show.blade.php`
   - Pembaruan tampilan varian di detail pesanan

3. `kedai-kiosk/resources/views/admin/orders/index.blade.php`
   - Penambahan indikator status varian di daftar pesanan

4. `kedai-kiosk/app/Models/Category.php`
   - Penambahan cast untuk has_variants

5. Script perbaikan yang dibuat:
   - `fix_variants_final.php`
   - `direct_fix_variants.php`
   - `add_variant_column.sql`
   - `public/js/kiosk-variant.js`

## Catatan

Jika ada masalah lagi dengan varian di masa depan, pastikan:

1. Data `variantType` dalam format JSON untuk checkout memiliki nilai string: `'hot'` atau `'ice'`
2. Nilai varian dinormalisasi sebelum disimpan (lowercase dan trim)
3. Model OrderItem memiliki `variant_type` dalam array `$fillable`
4. Kategori produk yang memiliki varian memiliki atribut `has_variants = true` 
