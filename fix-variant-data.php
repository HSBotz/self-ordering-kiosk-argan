<?php
// Script untuk memperbaiki data varian dalam order_items untuk pesanan tertentu

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

echo "========== PERBAIKAN DATA VARIAN ==========\n\n";

try {
    // Koneksi ke database
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Koneksi database berhasil\n\n";

    // Periksa apakah kolom variant_type ada
    $columns = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'")->fetchAll();

    if (empty($columns)) {
        echo "⚠️ Kolom variant_type tidak ditemukan di tabel order_items!\n";
        echo "   Menambahkan kolom variant_type...\n";

        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "✅ Kolom variant_type berhasil ditambahkan\n\n";
    } else {
        echo "✅ Kolom variant_type sudah ada\n\n";
    }

    // Ambil ID pesanan dari parameter GET jika ada
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

    if ($orderId) {
        // Ambil detail pesanan
        echo "DETAIL PESANAN #$orderId:\n";
        $order = $pdo->query("SELECT * FROM orders WHERE id = $orderId")->fetch();

        if ($order) {
            echo "  Order Number: {$order['order_number']}\n";
            echo "  Tanggal: {$order['created_at']}\n";
            echo "  Status: {$order['status']}\n\n";

            // Ambil item pesanan
            $items = $pdo->query("SELECT oi.id, oi.product_id, p.name as product_name, oi.variant_type
                                FROM order_items oi
                                LEFT JOIN products p ON oi.product_id = p.id
                                WHERE oi.order_id = $orderId")->fetchAll();

            echo "Item pesanan sebelum diperbaiki:\n";
            foreach ($items as $item) {
                $variant = $item['variant_type'] ?: 'NULL';
                echo "  - ID: {$item['id']}, Produk: {$item['product_name']}, Varian: $variant\n";
            }

            echo "\n";

            // Perbaiki data varian
            echo "Memperbaiki data varian untuk pesanan #$orderId...\n";

            // Update item ganjil jadi ice, genap jadi hot (atau bisa dengan parameter GET)
            $variantTypeHot = isset($_GET['hot']) ? $_GET['hot'] : null;
            $variantTypeIce = isset($_GET['ice']) ? $_GET['ice'] : null;

            if ($variantTypeHot !== null) {
                $hotItems = explode(',', $variantTypeHot);
                if (!empty($hotItems)) {
                    $placeholders = implode(',', array_fill(0, count($hotItems), '?'));
                    $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'hot' WHERE id IN ($placeholders)");
                    $stmt->execute($hotItems);
                    $hotCount = $stmt->rowCount();
                    echo "✅ $hotCount item diupdate menjadi varian HOT\n";
                }
            }

            if ($variantTypeIce !== null) {
                $iceItems = explode(',', $variantTypeIce);
                if (!empty($iceItems)) {
                    $placeholders = implode(',', array_fill(0, count($iceItems), '?'));
                    $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'ice' WHERE id IN ($placeholders)");
                    $stmt->execute($iceItems);
                    $iceCount = $stmt->rowCount();
                    echo "✅ $iceCount item diupdate menjadi varian ICE\n";
                }
            }

            if ($variantTypeHot === null && $variantTypeIce === null) {
                // Default update - alternatif hot/ice berdasarkan ID
                $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0 AND order_id = $orderId");
                $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1 AND order_id = $orderId");
                echo "✅ Item dengan ID genap diupdate menjadi HOT\n";
                echo "✅ Item dengan ID ganjil diupdate menjadi ICE\n";
            }

            echo "\n";

            // Tampilkan hasil setelah update
            $updatedItems = $pdo->query("SELECT oi.id, oi.product_id, p.name as product_name, oi.variant_type
                                        FROM order_items oi
                                        LEFT JOIN products p ON oi.product_id = p.id
                                        WHERE oi.order_id = $orderId")->fetchAll();

            echo "Item pesanan setelah diperbaiki:\n";
            foreach ($updatedItems as $item) {
                $variant = $item['variant_type'] ?: 'NULL';
                echo "  - ID: {$item['id']}, Produk: {$item['product_name']}, Varian: $variant\n";
            }
        } else {
            echo "⚠️ Pesanan dengan ID #$orderId tidak ditemukan!\n";
        }
    } else {
        echo "Untuk memperbaiki pesanan tertentu, gunakan parameter order_id di URL.\n";
        echo "Contoh: fix-variant-data.php?order_id=31\n\n";

        echo "Anda juga dapat menentukan item mana yang akan diupdate ke hot/ice:\n";
        echo "Contoh: fix-variant-data.php?order_id=31&hot=1,3,5&ice=2,4,6\n";
        echo "Parameter hot dan ice berisi ID item yang akan diupdate, dipisahkan koma.\n\n";

        echo "Daftar 10 pesanan terbaru:\n";
        $orders = $pdo->query("SELECT id, order_number, customer_name, created_at
                              FROM orders
                              ORDER BY id DESC
                              LIMIT 10")->fetchAll();

        foreach ($orders as $ord) {
            echo "  - ID: {$ord['id']}, No: {$ord['order_number']}, ";
            echo "Pelanggan: {$ord['customer_name']}, Tanggal: {$ord['created_at']}\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n========== SELESAI ==========\n";
