<?php
// Script sederhana hanya untuk menambahkan kolom variant_type

// Set header content type
header('Content-Type: text/plain');
echo "Memperbaiki tabel order_items...\n\n";

try {
    // Connect to database
    $db = new PDO('mysql:host=localhost;dbname=kedai_kiosk', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil\n";

    // Add variant_type column if it doesn't exist
    $sql = "ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes";
    $db->exec($sql);
    echo "Kolom variant_type berhasil ditambahkan\n";

} catch(PDOException $e) {
    // If column already exists, this is normal
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Kolom variant_type sudah ada\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\nPerbaikan selesai!";
