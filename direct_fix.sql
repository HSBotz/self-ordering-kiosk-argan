-- Script SQL untuk menambahkan kolom variant_type ke tabel order_items
-- File ini dapat dijalankan langsung di phpMyAdmin

-- Langsung tambahkan kolom (akan error jika sudah ada tapi tidak masalah)
ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;

-- Atau gunakan cara yang lebih aman (tidak error jika kolom sudah ada)
-- Cara di bawah ini memerlukan privileges lebih tinggi, uncomment jika diperlukan
/*
SET @dbname = 'kedai_kiosk';
SET @tablename = 'order_items';
SET @columnname = 'variant_type';
SET @datatype = 'VARCHAR(10)';

SELECT IF(
  EXISTS(
    SELECT * FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ),
  CONCAT('SELECT "Column ', @columnname, ' already exists in table ', @tablename, '";'),
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @datatype, ' NULL AFTER notes;')
) INTO @sql;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
*/
