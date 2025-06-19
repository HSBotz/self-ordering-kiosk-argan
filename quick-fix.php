<?php
// Koneksi ke database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Set output sebagai teks
header('Content-Type: text/plain');

echo "===== DATABASE FIX UTILITY =====\n\n";

try {
    // Koneksi ke database
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✓ Koneksi ke database berhasil\n\n";

    // Cek tabel order_items
    $tables = $pdo->query("SHOW TABLES LIKE 'order_items'")->fetchAll();
    if (count($tables) > 0) {
        echo "✓ Tabel order_items ditemukan\n\n";

        // Dapatkan struktur tabel
        echo "Struktur tabel order_items:\n";
        $columns = $pdo->query("SHOW COLUMNS FROM order_items")->fetchAll();
        $variantTypeExists = false;

        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
            if ($column['Field'] === 'variant_type') {
                $variantTypeExists = true;
            }
        }

        echo "\n";

        // Tambahkan kolom variant_type jika belum ada
        if (!$variantTypeExists) {
            echo "Kolom variant_type tidak ditemukan, menambahkan...\n";
            $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
            echo "✓ Kolom variant_type berhasil ditambahkan\n\n";
        } else {
            echo "✓ Kolom variant_type sudah ada\n\n";
        }

        // Cek data dalam tabel
        echo "Sample data dari order_items:\n";
        $items = $pdo->query("SELECT * FROM order_items LIMIT 5")->fetchAll();

        if (count($items) > 0) {
            foreach ($items as $item) {
                $variant = isset($item['variant_type']) ? $item['variant_type'] : 'NULL';
                echo "- ID: {$item['id']}, Order: {$item['order_id']}, Product: {$item['product_id']}, ";
                echo "Qty: {$item['quantity']}, Variant: $variant\n";
            }
        } else {
            echo "Tidak ada data dalam tabel\n";
        }
    } else {
        echo "❌ Tabel order_items tidak ditemukan\n";
    }

    // Script untuk memperbaiki nilai ice/hot yang tidak disertai tanda kutip
    echo "\n===== PERBAIKAN CONTROLLER =====\n";
    echo "Untuk memperbaiki masalah nilai varian yang tidak disertai tanda kutip,\n";
    echo "pastikan di KioskController.php sudah menerapkan perbaikan:\n\n";
    echo "// Pastikan variant_type yang disimpan dalam quote jika ada\n";
    echo "\$variantType = isset(\$item['variantType']) && !empty(\$item['variantType'])\n";
    echo "    ? \$item['variantType']\n";
    echo "    : null;\n\n";
    echo "OrderItem::create([\n";
    echo "    ...\n";
    echo "    'variant_type' => \$variantType,\n";
    echo "]);\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n===== SELESAI =====\n";
