<?php
// Script untuk memeriksa data varian dalam order_items

// Set output sebagai text
header('Content-Type: text/plain');

// Koneksi ke database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

echo "========== PEMERIKSAAN DATA VARIAN ==========\n\n";

try {
    // Koneksi ke database
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Koneksi database berhasil\n\n";

    // 1. Periksa struktur kolom variant_type
    echo "STRUKTUR KOLOM VARIANT_TYPE:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'")->fetchAll();
    foreach ($columns as $col) {
        echo "  - Field: {$col['Field']}\n";
        echo "  - Type: {$col['Type']}\n";
        echo "  - Null: {$col['Null']}\n";
        echo "  - Key: {$col['Key']}\n";
        echo "  - Default: " . ($col['Default'] ?? "NULL") . "\n";
        echo "  - Extra: {$col['Extra']}\n";
    }

    if (empty($columns)) {
        echo "⚠️ Kolom variant_type tidak ditemukan di tabel order_items!\n";
        echo "   Jalankan skrip berikut untuk menambahkan kolom:\n";
        echo "   ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes;\n\n";
        die();
    }

    echo "\n";

    // 2. Periksa data dalam tabel
    echo "DATA VARIAN DI ORDER ITEMS:\n";
    $items = $pdo->query("SELECT oi.id, oi.order_id, oi.product_id, p.name as product_name, oi.variant_type
                          FROM order_items oi
                          LEFT JOIN products p ON oi.product_id = p.id
                          ORDER BY oi.order_id DESC
                          LIMIT 20")->fetchAll();

    echo sprintf("%-5s %-10s %-30s %-10s\n", "ID", "Order ID", "Produk", "Varian");
    echo str_repeat("-", 60) . "\n";

    foreach ($items as $item) {
        $variant = $item['variant_type'] ?: 'NULL';
        echo sprintf("%-5s %-10s %-30s %-10s\n",
            $item['id'],
            $item['order_id'],
            substr($item['product_name'] ?? "ID:{$item['product_id']}", 0, 28),
            $variant
        );
    }

    echo "\n";

    // 3. Periksa data dengan ID pesanan tertentu jika ada
    if (isset($_GET['order_id'])) {
        $orderId = (int)$_GET['order_id'];
        echo "DETAIL PESANAN #$orderId:\n";
        $orderItems = $pdo->query("SELECT oi.id, oi.product_id, p.name as product_name, oi.quantity, oi.price,
                                   oi.variant_type, oi.notes, oi.created_at
                                   FROM order_items oi
                                   LEFT JOIN products p ON oi.product_id = p.id
                                   WHERE oi.order_id = $orderId")->fetchAll();

        if (empty($orderItems)) {
            echo "Tidak ada item untuk pesanan #$orderId\n";
        } else {
            echo sprintf("%-5s %-30s %-10s %-10s %-10s %-15s\n",
                "ID", "Produk", "Jumlah", "Harga", "Varian", "Dibuat pada");
            echo str_repeat("-", 80) . "\n";

            foreach ($orderItems as $item) {
                $variant = $item['variant_type'] ?: 'NULL';
                echo sprintf("%-5s %-30s %-10s %-10s %-10s %-15s\n",
                    $item['id'],
                    substr($item['product_name'] ?? "ID:{$item['product_id']}", 0, 28),
                    $item['quantity'],
                    number_format($item['price'], 0, ',', '.'),
                    $variant,
                    $item['created_at']
                );
            }
        }

        echo "\n";
    }

    // 4. Hitung jumlah varian panas dan dingin
    $counts = $pdo->query("SELECT variant_type, COUNT(*) as count
                           FROM order_items
                           WHERE variant_type IS NOT NULL
                           GROUP BY variant_type")->fetchAll();

    echo "STATISTIK VARIAN:\n";
    foreach ($counts as $count) {
        echo "  - {$count['variant_type']}: {$count['count']} item\n";
    }

    // 5. Periksa nilai NULL
    $nullCount = $pdo->query("SELECT COUNT(*) as count
                              FROM order_items
                              WHERE variant_type IS NULL")->fetch();

    echo "  - NULL: {$nullCount['count']} item\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n========== SELESAI ==========\n";
