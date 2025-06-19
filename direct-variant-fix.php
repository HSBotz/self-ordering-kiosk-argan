<?php
// Script untuk memperbaiki kolom variant_type di database

// Koneksi database
$host = 'localhost';
$database = 'kedai_kiosk';
$username = 'root';
$password = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi ke database berhasil.\n";

    // 1. Periksa apakah kolom variant_type ada
    $checkColumn = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'");
    if ($checkColumn->rowCount() == 0) {
        // Kolom tidak ada, tambahkan
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "Kolom variant_type ditambahkan.\n";
    } else {
        echo "Kolom variant_type sudah ada.\n";
    }

    // 2. Ambil sample data dari tabel order_items untuk diperiksa
    $items = $pdo->query("SELECT * FROM order_items LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample data dari order_items:\n";
    foreach ($items as $item) {
        echo "ID: {$item['id']}, Order ID: {$item['order_id']}, Product: {$item['product_id']}, Variant: " .
             (isset($item['variant_type']) ? $item['variant_type'] : 'NULL') . "\n";
    }

    // 3. Update data untuk pengujian (opsional)
    // Misalnya, set beberapa item dengan variant hot/ice
    echo "\nMemperbarui beberapa item untuk pengujian...\n";

    // Update beberapa item menjadi hot
    $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0 AND variant_type IS NULL LIMIT 5");
    echo "5 item pertama dengan ID genap diubah menjadi hot.\n";

    // Update beberapa item menjadi ice
    $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1 AND variant_type IS NULL LIMIT 5");
    echo "5 item pertama dengan ID ganjil diubah menjadi ice.\n";

    // 4. Cek lagi data setelah diupdate
    $updatedItems = $pdo->query("SELECT * FROM order_items WHERE variant_type IS NOT NULL LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nData setelah diupdate:\n";

    if (count($updatedItems) > 0) {
        foreach ($updatedItems as $item) {
            echo "ID: {$item['id']}, Order ID: {$item['order_id']}, Product: {$item['product_id']}, Variant: {$item['variant_type']}\n";
        }
    } else {
        echo "Tidak ada item yang memiliki varian.\n";
    }

    echo "\nProses selesai!\n";

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
