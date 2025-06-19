-- Script SQL untuk memperbaiki kolom variant_type dan datanya

-- 1. Cek apakah kolom variant_type sudah ada di tabel order_items
-- Jika belum, kita akan menambahkannya
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS variant_type VARCHAR(10) NULL AFTER notes;

-- 2. Update nilai variant_type untuk item dengan ID genap menjadi 'hot'
UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0;

-- 3. Update nilai variant_type untuk item dengan ID ganjil menjadi 'ice'
UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1;

-- 4. Tampilkan hasil setelah update
SELECT 'Statistik variant_type' as info,
       COUNT(*) AS total_items,
       SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
       SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
       SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
FROM order_items;

-- 5. Tampilkan beberapa contoh data
SELECT id, order_id, product_id, variant_type
FROM order_items
LIMIT 10;
