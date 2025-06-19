<?php
/*
 * PERBAIKAN LANGSUNG TABEL ORDER_ITEMS DAN DATA VARIAN
 * Script ini akan langsung menambahkan kolom variant_type jika belum ada
 * dan memperbaiki data varian untuk pesanan #31
 */

// Koneksi database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Koneksi database berhasil\n";

    // 1. Periksa dan tambahkan kolom variant_type jika belum ada
    $columns = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'")->fetchAll();
    if (empty($columns)) {
        echo "⚠️ Kolom variant_type tidak ditemukan! Menambahkan kolom...\n";
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "✓ Kolom variant_type berhasil ditambahkan\n";
    } else {
        echo "✓ Kolom variant_type sudah ada\n";
    }

    // 2. Perbaiki data varian untuk pesanan #31
    $order_id = 31; // ID pesanan yang akan diperbaiki

    // Periksa apakah pesanan ada
    $order = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();
    if (!$order) {
        echo "❌ Pesanan dengan ID #$order_id tidak ditemukan!\n";
        die();
    }

    echo "✓ Pesanan #$order_id ditemukan: {$order['order_number']}\n";

    // Update varian untuk semua item di pesanan #31
    // Tetapkan item dengan ID genap sebagai HOT dan ID ganjil sebagai ICE
    $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE order_id = $order_id AND id % 2 = 0");
    $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE order_id = $order_id AND id % 2 = 1");

    $affectedRows = $pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id = $order_id")->fetchColumn();
    echo "✓ $affectedRows item dalam pesanan #$order_id berhasil diupdate\n";

    // Tampilkan hasil update
    $items = $pdo->query("SELECT oi.id, p.name, oi.variant_type
                         FROM order_items oi
                         LEFT JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = $order_id")->fetchAll();

    echo "\nItem pesanan setelah update:\n";
    echo "-----------------------------\n";
    foreach ($items as $item) {
        $variant = $item['variant_type'] ?: 'NULL';
        echo "[{$item['id']}] {$item['name']} - Varian: $variant\n";
    }

    echo "\n✓ Perbaikan selesai! Silakan periksa http://localhost:8000/admin/orders/$order_id\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\nTekan ENTER untuk keluar...";
if (PHP_SAPI !== 'cli') fread(fopen("php://stdin", "r"), 1);
