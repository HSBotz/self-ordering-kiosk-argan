-- Script SQL untuk perbaikan langsung varian hot/ice di tabel order_items

-- 1. Cek apakah kolom variant_type sudah ada
SELECT COUNT(*) AS column_exists
FROM information_schema.columns
WHERE table_schema = 'kedai_kiosk'
AND table_name = 'order_items'
AND column_name = 'variant_type';

-- 2. Tambahkan kolom variant_type jika belum ada
-- ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;

-- 3. Atur varian untuk pesanan #31 (varians alternatif)
UPDATE order_items SET variant_type = 'hot' WHERE order_id = 31 AND id % 2 = 0;
UPDATE order_items SET variant_type = 'ice' WHERE order_id = 31 AND id % 2 = 1;

-- 4. Perbaiki semua item tanpa varian
UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0;
UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1;

-- 5. Cek hasil perbaikan untuk pesanan #31
SELECT oi.id, p.name, oi.variant_type
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 31
ORDER BY oi.id;

-- 6. Statistik hasil perbaikan
SELECT
    COUNT(*) AS total_items,
    SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
    SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
    SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
FROM order_items;
