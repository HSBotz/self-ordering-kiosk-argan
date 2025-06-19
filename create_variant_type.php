<?php
// Script untuk memastikan kolom variant_type ada di tabel order_items
// Dan menambahkannya jika belum ada

$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil\n";

    // Periksa apakah kolom variant_type sudah ada
    $query = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = '$db'
              AND TABLE_NAME = 'order_items'
              AND COLUMN_NAME = 'variant_type'";

    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] == 0) {
        echo "Kolom variant_type tidak ditemukan. Menambahkan kolom...\n";

        // Tambahkan kolom variant_type jika belum ada
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");

        echo "Kolom variant_type berhasil ditambahkan\n";
    } else {
        echo "Kolom variant_type sudah ada di tabel order_items\n";

        // Periksa tipe data kolom
        $query = "SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = '$db'
                 AND TABLE_NAME = 'order_items'
                 AND COLUMN_NAME = 'variant_type'";

        $stmt = $pdo->query($query);
        $column = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "Tipe data: " . $column['DATA_TYPE'];
        if (!empty($column['CHARACTER_MAXIMUM_LENGTH'])) {
            echo "(" . $column['CHARACTER_MAXIMUM_LENGTH'] . ")";
        }
        echo "\n";
    }

    // Periksa berapa item yang tidak memiliki nilai variant_type
    $stmt = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL");
    $nullCount = $stmt->fetchColumn();
    echo "Jumlah item tanpa nilai variant_type: $nullCount\n";

    if ($nullCount > 0) {
        // Set item dengan ID genap sebagai HOT
        $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0");
        $hotCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type = 'hot'")->fetchColumn();
        echo "Item dengan variant_type = 'hot' sekarang: $hotCount\n";

        // Set item dengan ID ganjil sebagai ICE
        $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1");
        $iceCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type = 'ice'")->fetchColumn();
        echo "Item dengan variant_type = 'ice' sekarang: $iceCount\n";
    }

    // Verifikasi tidak ada lagi item tanpa variant_type
    $stmt = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL");
    $nullAfter = $stmt->fetchColumn();
    echo "Jumlah item tanpa nilai variant_type setelah perbaikan: $nullAfter\n";

    // Tampilkan beberapa contoh data
    echo "\nSampel data order_items:\n";
    $stmt = $pdo->query("SELECT id, order_id, product_id, variant_type FROM order_items LIMIT 10");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        echo "ID: {$item['id']}, OrderID: {$item['order_id']}, ProductID: {$item['product_id']}, Variant: {$item['variant_type']}\n";
    }

} catch (PDOException $e) {
    echo "Error database: " . $e->getMessage() . "\n";
}
