-- SQL script untuk menambahkan kolom variant_type ke tabel order_items

-- Cek apakah kolom variant_type sudah ada
SET @exist := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'kedai_kiosk'
    AND TABLE_NAME = 'order_items'
    AND COLUMN_NAME = 'variant_type'
);

-- Tambahkan kolom hanya jika belum ada
SET @query = IF(@exist = 0,
    'ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;',
    'SELECT "Kolom variant_type sudah ada pada tabel order_items" AS Status;'
);

PREPARE alterIfNotExists FROM @query;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Set variant_type untuk pesanan #31
UPDATE order_items SET variant_type = 'hot' WHERE order_id = 31 AND id % 2 = 0;
UPDATE order_items SET variant_type = 'ice' WHERE order_id = 31 AND id % 2 = 1;

-- Tampilkan status pesanan #31 setelah update
SELECT oi.id, p.name, oi.variant_type
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 31
ORDER BY oi.id;
