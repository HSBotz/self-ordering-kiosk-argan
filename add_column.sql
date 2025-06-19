-- Add has_variants column to categories table
ALTER TABLE categories ADD COLUMN has_variants TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;

-- SQL script untuk menambahkan kolom variant_type ke tabel order_items
-- Periksa apakah kolom variant_type sudah ada
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'kedai_kiosk'
AND TABLE_NAME = 'order_items'
AND COLUMN_NAME = 'variant_type';

-- Tambahkan kolom jika belum ada
SET @query = IF(@col_exists = 0,
    'ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes',
    'SELECT "Kolom variant_type sudah ada"');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update beberapa data untuk pengujian
UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0 AND variant_type IS NULL LIMIT 5;
UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1 AND variant_type IS NULL LIMIT 5;
