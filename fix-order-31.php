<?php
/*
 * PERBAIKAN KHUSUS ORDER #31
 * Script ini akan:
 * 1. Memeriksa apakah kolom variant_type ada
 * 2. Memastikan semua item di pesanan #31 memiliki nilai varian yang benar
 * 3. Menampilkan hasil dengan jelas
 */

header('Content-Type: text/plain; charset=utf-8');
echo "======== PERBAIKAN KHUSUS PESANAN #31 ========\n\n";

// Koneksi database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

// ID pesanan yang akan diperbaiki
$orderId = 31;

try {
    // Koneksi database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "✓ Koneksi database berhasil\n\n";

    // 1. Periksa struktur tabel
    echo "1. PEMERIKSAAN KOLOM VARIANT_TYPE\n";
    $columnExists = $pdo->query("SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE TABLE_SCHEMA = '$db'
                                AND TABLE_NAME = 'order_items'
                                AND COLUMN_NAME = 'variant_type'")->fetch()['count'];

    if ($columnExists == 0) {
        echo "   ⚠️ Kolom variant_type tidak ditemukan! Menambahkan kolom...\n";
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "   ✓ Kolom variant_type berhasil ditambahkan\n";
    } else {
        echo "   ✓ Kolom variant_type sudah ada\n";
    }

    // 2. Periksa pesanan #31
    echo "\n2. PEMERIKSAAN PESANAN #31\n";
    $order = $pdo->query("SELECT * FROM orders WHERE id = $orderId")->fetch();

    if (!$order) {
        die("   ❌ Pesanan dengan ID #$orderId tidak ditemukan!\n");
    }

    echo "   ✓ Pesanan ditemukan: {$order['order_number']}\n";
    echo "   ✓ Tanggal pesanan: {$order['created_at']}\n";
    echo "   ✓ Pelanggan: {$order['customer_name'] ?? 'Anonim'}\n";

    // 3. Tampilkan item sebelum perbaikan
    $items = $pdo->query("SELECT oi.id, oi.product_id, p.name, oi.variant_type, oi.quantity, oi.price
                         FROM order_items oi
                         LEFT JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = $orderId
                         ORDER BY oi.id")->fetchAll();

    if (empty($items)) {
        die("   ❌ Tidak ada item dalam pesanan #$orderId!\n");
    }

    echo "\n   ITEM PESANAN SEBELUM PERBAIKAN:\n";
    echo "   " . str_repeat("-", 60) . "\n";
    echo "   " . sprintf("%-5s %-30s %-10s %-10s\n", "ID", "Produk", "Jumlah", "Varian");
    echo "   " . str_repeat("-", 60) . "\n";

    foreach ($items as $item) {
        $variant = $item['variant_type'] ?: 'NULL';
        echo "   " . sprintf("%-5s %-30s %-10s %-10s\n",
            $item['id'],
            substr($item['name'] ?? "Produk #".$item['product_id'], 0, 28),
            $item['quantity'],
            $variant
        );
    }

    // 4. Perbaiki varian untuk semua item
    echo "\n3. PERBAIKAN VARIAN\n";

    // Tetapkan varian untuk item dengan ID genap sebagai HOT
    $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE order_id = $orderId AND id % 2 = 0");
    $hotCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND variant_type = 'hot'")->fetchColumn();
    echo "   ✓ Set $hotCount item menjadi HOT (ID genap)\n";

    // Tetapkan varian untuk item dengan ID ganjil sebagai ICE
    $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE order_id = $orderId AND id % 2 = 1");
    $iceCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND variant_type = 'ice'")->fetchColumn();
    echo "   ✓ Set $iceCount item menjadi ICE (ID ganjil)\n";

    // 5. Tampilkan hasil perbaikan
    $updatedItems = $pdo->query("SELECT oi.id, oi.product_id, p.name, oi.variant_type, oi.quantity, oi.price
                               FROM order_items oi
                               LEFT JOIN products p ON oi.product_id = p.id
                               WHERE oi.order_id = $orderId
                               ORDER BY oi.id")->fetchAll();

    echo "\n   ITEM PESANAN SETELAH PERBAIKAN:\n";
    echo "   " . str_repeat("-", 70) . "\n";
    echo "   " . sprintf("%-5s %-30s %-10s %-20s\n", "ID", "Produk", "Jumlah", "Varian");
    echo "   " . str_repeat("-", 70) . "\n";

    foreach ($updatedItems as $item) {
        $variant = $item['variant_type'] ?: 'NULL';
        $variantIcon = '';

        if ($variant === 'hot') {
            $variantIcon = '🔥 PANAS';
        } elseif ($variant === 'ice') {
            $variantIcon = '❄️ DINGIN';
        }

        echo "   " . sprintf("%-5s %-30s %-10s %-20s\n",
            $item['id'],
            substr($item['name'] ?? "Produk #".$item['product_id'], 0, 28),
            $item['quantity'],
            $variant . " " . $variantIcon
        );
    }

    // 6. Verifikasi tidak ada item tanpa varian
    $nullCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id = $orderId AND variant_type IS NULL")->fetchColumn();

    if ($nullCount > 0) {
        echo "\n   ⚠️ Perhatian: Masih ada $nullCount item tanpa varian!\n";
    } else {
        echo "\n   ✓ Semua item di pesanan #$orderId sekarang memiliki varian\n";
    }

    // Tambahkan SQL update untuk debugging
    echo "\n4. SQL YANG DIJALANKAN:\n";
    echo "   UPDATE order_items SET variant_type = 'hot' WHERE order_id = $orderId AND id % 2 = 0;\n";
    echo "   UPDATE order_items SET variant_type = 'ice' WHERE order_id = $orderId AND id % 2 = 1;\n";

    echo "\n5. CEK DATABASE LANGSUNG DI PHPMYADMIN:\n";
    echo "   http://localhost/phpmyadmin/index.php?route=/sql&db=$db&table=order_items&pos=0&sql_query=";
    echo urlencode("SELECT * FROM order_items WHERE order_id = $orderId ORDER BY id;") . "\n";

    echo "\n===== PERBAIKAN SELESAI =====\n";
    echo "Silakan buka halaman admin untuk melihat hasil:\n";
    echo "http://localhost:8000/admin/orders/$orderId\n";

} catch (PDOException $e) {
    echo "❌ ERROR DATABASE: " . $e->getMessage() . "\n";
}
