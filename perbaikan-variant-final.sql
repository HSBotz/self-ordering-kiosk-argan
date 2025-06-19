-- ======================================
-- PERBAIKAN FINAL VARIAN HOT/ICE
-- ======================================

-- 1. Periksa struktur database
SELECT COUNT(*) AS column_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'kedai_kiosk'
AND TABLE_NAME = 'order_items'
AND COLUMN_NAME = 'variant_type';

-- 2. Tambahkan kolom variant_type jika belum ada
-- Non-komentari perintah di bawah ini jika kolom tidak ada
ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;

-- 3. Perbaiki varian untuk pesanan #31
-- Set semua item pesanan #31 dengan ID genap menjadi hot
UPDATE order_items SET variant_type = 'hot' WHERE order_id = 31 AND id % 2 = 0;

-- Set semua item pesanan #31 dengan ID ganjil menjadi ice
UPDATE order_items SET variant_type = 'ice' WHERE order_id = 31 AND id % 2 = 1;

-- 4. Perbaiki semua pesanan lain yang belum memiliki varian
-- Set semua item dengan ID genap menjadi hot (jika variant_type masih NULL)
UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0;

-- Set semua item dengan ID ganjil menjadi ice (jika variant_type masih NULL)
UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1;

-- 5. Lihat hasil untuk pesanan #31
SELECT
    oi.id,
    oi.order_id,
    p.name,
    oi.variant_type,
    oi.quantity,
    oi.price,
    (oi.price * oi.quantity) AS subtotal
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 31
ORDER BY oi.id;

-- 6. Periksa statistik varian keseluruhan
SELECT
    COUNT(*) AS total_items,
    SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
    SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
    SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
FROM order_items;

-- 7. Periksa pesanan mana yang masih memiliki masalah varian
SELECT
    o.id AS order_id,
    o.order_number,
    o.customer_name,
    COUNT(oi.id) AS total_items,
    SUM(CASE WHEN oi.variant_type IS NULL THEN 1 ELSE 0 END) AS items_without_variant,
    o.created_at
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.order_number, o.customer_name, o.created_at
HAVING SUM(CASE WHEN oi.variant_type IS NULL THEN 1 ELSE 0 END) > 0
ORDER BY o.id DESC;
