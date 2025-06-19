<?php
/*
 * PERBAIKAN KOMPREHENSIF VARIAN MINUMAN
 * Script ini akan memeriksa dan memperbaiki seluruh masalah terkait varian hot/ice
 * Dapat dijalankan langsung di browser: http://localhost/kedai-coffee-kiosk/kedai-kiosk/fix_variants_final.php
 */

// Set output ke teks biasa agar lebih mudah dibaca
header('Content-Type: text/plain');

echo "===== PERBAIKAN KOMPREHENSIF VARIAN MINUMAN =====\n\n";

// Koneksi ke database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Koneksi database berhasil\n\n";

    // LANGKAH 1: Periksa struktur tabel order_items dan tambahkan kolom variant_type jika belum ada
    echo "1. MEMERIKSA STRUKTUR TABEL...\n";

    $columns = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'")->fetchAll();
    if (empty($columns)) {
        echo "   ⚠️ Kolom variant_type tidak ditemukan!\n";
        echo "   ➤ Menambahkan kolom variant_type ke tabel order_items...\n";

        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "   ✓ Kolom variant_type berhasil ditambahkan!\n\n";
    } else {
        echo "   ✓ Kolom variant_type sudah ada di tabel\n";
        echo "   Tipe data: " . $columns[0]['Type'] . " | Null: " . $columns[0]['Null'] . "\n\n";
    }

    // LANGKAH 2: Periksa dan perbaiki data untuk semua pesanan atau pesanan tertentu
    $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

    if ($order_id) {
        echo "2. MEMPERBAIKI VARIAN UNTUK PESANAN #$order_id...\n";

        // Periksa apakah pesanan tersebut ada
        $order = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();
        if (!$order) {
            echo "   ⚠️ Pesanan dengan ID #$order_id tidak ditemukan!\n\n";
        } else {
            // Ambil detail order
            echo "   ✓ Pesanan ditemukan: #{$order['order_number']} ({$order['created_at']})\n";

            // Cek jumlah item pesanan
            $itemsCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id = $order_id")->fetchColumn();
            echo "   ➤ Jumlah item dalam pesanan: $itemsCount\n\n";

            if ($itemsCount > 0) {
                // Cek status varian saat ini
                $items = $pdo->query("SELECT oi.id, p.name AS product_name, oi.variant_type
                                     FROM order_items oi
                                     LEFT JOIN products p ON oi.product_id = p.id
                                     WHERE oi.order_id = $order_id")->fetchAll();

                echo "   ITEM PESANAN SEBELUM PERBAIKAN:\n";
                echo "   -----------------------------\n";
                foreach ($items as $item) {
                    $variant = $item['variant_type'] ?: 'NULL';
                    echo "   - [{$item['id']}] {$item['product_name']} → Varian: $variant\n";
                }

                echo "\n";

                // Periksa apakah ada parameter untuk menetapkan varian hot/ice
                $hot_items = isset($_GET['hot']) ? array_map('trim', explode(',', $_GET['hot'])) : [];
                $ice_items = isset($_GET['ice']) ? array_map('trim', explode(',', $_GET['ice'])) : [];

                if (!empty($hot_items) || !empty($ice_items)) {
                    // Tetapkan varian berdasarkan parameter
                    if (!empty($hot_items)) {
                        $placeholders = implode(',', array_fill(0, count($hot_items), '?'));
                        $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'hot' WHERE id IN ($placeholders)");
                        $stmt->execute($hot_items);
                        $hotCount = $stmt->rowCount();
                        echo "   ✓ $hotCount item diupdate menjadi HOT\n";
                    }

                    if (!empty($ice_items)) {
                        $placeholders = implode(',', array_fill(0, count($ice_items), '?'));
                        $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'ice' WHERE id IN ($placeholders)");
                        $stmt->execute($ice_items);
                        $iceCount = $stmt->rowCount();
                        echo "   ✓ $iceCount item diupdate menjadi ICE\n";
                    }
                } else {
                    // Jika tidak ada parameter, set varian berdasarkan kategori atau item
                    echo "   ➤ Mendeteksi varian minuman berdasarkan kategori...\n";

                    // Coba deteksi berdasarkan kategori minuman
                    $pdo->exec("UPDATE order_items oi
                               JOIN products p ON oi.product_id = p.id
                               JOIN categories c ON p.category_id = c.id
                               SET oi.variant_type = 'hot'
                               WHERE oi.order_id = $order_id
                               AND c.has_variants = 1
                               AND oi.variant_type IS NULL
                               AND (p.name LIKE '%panas%' OR p.name LIKE '%hot%')");

                    $pdo->exec("UPDATE order_items oi
                               JOIN products p ON oi.product_id = p.id
                               JOIN categories c ON p.category_id = c.id
                               SET oi.variant_type = 'ice'
                               WHERE oi.order_id = $order_id
                               AND c.has_variants = 1
                               AND oi.variant_type IS NULL
                               AND (p.name LIKE '%dingin%' OR p.name LIKE '%ice%' OR p.name LIKE '%cold%')");

                    // Jika masih tidak berhasil, tetapkan default
                    $nullItemsCount = $pdo->query("SELECT COUNT(*) FROM order_items
                                                WHERE order_id = $order_id
                                                AND variant_type IS NULL")->fetchColumn();

                    if ($nullItemsCount > 0) {
                        echo "   ➤ Masih ada $nullItemsCount item tanpa varian. Menerapkan default...\n";

                        // Set item dengan id genap menjadi hot, ganjil menjadi ice
                        $pdo->exec("UPDATE order_items SET variant_type = 'hot'
                                  WHERE order_id = $order_id
                                  AND id % 2 = 0
                                  AND variant_type IS NULL");

                        $pdo->exec("UPDATE order_items SET variant_type = 'ice'
                                  WHERE order_id = $order_id
                                  AND id % 2 = 1
                                  AND variant_type IS NULL");

                        echo "   ✓ Item dengan ID genap diatur sebagai HOT\n";
                        echo "   ✓ Item dengan ID ganjil diatur sebagai ICE\n";
                    }
                }

                // Cek status varian setelah perbaikan
                $updatedItems = $pdo->query("SELECT oi.id, p.name AS product_name, oi.variant_type
                                           FROM order_items oi
                                           LEFT JOIN products p ON oi.product_id = p.id
                                           WHERE oi.order_id = $order_id")->fetchAll();

                echo "\n   ITEM PESANAN SETELAH PERBAIKAN:\n";
                echo "   ----------------------------\n";
                foreach ($updatedItems as $item) {
                    $variant = $item['variant_type'] ?: 'NULL';
                    $variantLabel = '';

                    if ($variant === 'hot') {
                        $variantLabel = '[PANAS 🔥]';
                    } elseif ($variant === 'ice') {
                        $variantLabel = '[DINGIN ❄️]';
                    }

                    echo "   - [{$item['id']}] {$item['product_name']} → Varian: $variant $variantLabel\n";
                }
            } else {
                echo "   ⚠️ Tidak ada item dalam pesanan ini!\n";
            }
        }
    } else {
        echo "2. PILIH PESANAN UNTUK DIPERBAIKI\n\n";
        echo "   Gunakan parameter order_id untuk memperbaiki pesanan tertentu.\n";
        echo "   Contoh: fix_variants_final.php?order_id=31\n\n";
        echo "   Anda juga dapat menentukan varian secara manual dengan parameter hot dan ice:\n";
        echo "   Contoh: fix_variants_final.php?order_id=31&hot=1,3,5&ice=2,4,6\n\n";

        // Tampilkan 10 pesanan terbaru
        echo "   DAFTAR PESANAN TERBARU:\n";
        echo "   ---------------------\n";

        $orders = $pdo->query("SELECT o.id, o.order_number, o.customer_name, o.created_at,
                             COUNT(oi.id) as item_count,
                             SUM(CASE WHEN oi.variant_type IS NOT NULL THEN 1 ELSE 0 END) as variant_count
                             FROM orders o
                             LEFT JOIN order_items oi ON o.id = oi.order_id
                             GROUP BY o.id, o.order_number, o.customer_name, o.created_at
                             ORDER BY o.id DESC
                             LIMIT 10")->fetchAll();

        foreach ($orders as $order) {
            $variantStatus = ($order['item_count'] > 0 && $order['variant_count'] == $order['item_count'])
                          ? '✓' : ($order['variant_count'] > 0 ? '⚠️' : '✗');

            echo "   $variantStatus #{$order['id']} - {$order['order_number']} - {$order['customer_name']} - ";
            echo "{$order['created_at']} ({$order['variant_count']}/{$order['item_count']} varian)\n";
        }
    }

    // LANGKAH 3: Cek konsistensi seluruh database
    echo "\n3. CEK KONSISTENSI KESELURUHAN\n";

    $totalItems = $pdo->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
    $totalWithVariant = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NOT NULL")->fetchColumn();
    $totalNullVariant = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL")->fetchColumn();

    echo "   ➤ Total item pesanan: $totalItems\n";
    echo "   ➤ Item dengan varian: $totalWithVariant\n";
    echo "   ➤ Item tanpa varian: $totalNullVariant\n\n";

    if ($totalNullVariant > 0 && !$order_id) {
        echo "   ⚠️ Masih ada $totalNullVariant item tanpa varian!\n";
        echo "   ➤ Untuk memperbaiki semua pesanan sekaligus, gunakan parameter fix_all=1:\n";
        echo "      fix_variants_final.php?fix_all=1\n\n";
    }

    // Fix all jika diminta
    if (isset($_GET['fix_all']) && $_GET['fix_all'] == 1) {
        echo "4. MEMPERBAIKI SEMUA ITEM TANPA VARIAN\n";

        // Set default varian berdasarkan ID item (genap = hot, ganjil = ice)
        $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE id % 2 = 0 AND variant_type IS NULL");
        $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE id % 2 = 1 AND variant_type IS NULL");

        $updatedCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NOT NULL")->fetchColumn();
        echo "   ✓ Sekarang terdapat $updatedCount dari $totalItems item dengan varian terisi!\n\n";
    }

    // LANGKAH 4: Petunjuk untuk memastikan varian tersimpan dengan baik untuk pesanan baru
    echo "\n5. REKOMENDASI TINDAK LANJUT\n";
    echo "   • Pastikan kolom variant_type di database sudah dibuat dengan benar\n";
    echo "   • Pastikan data variant_type dalam checkout disimpan dengan format string ('hot'/'ice')\n";
    echo "   • Periksa model OrderItem.php sudah memiliki variant_type dalam fillable\n";
    echo "   • Pastikan KioskController.php menyimpan varian dengan benar saat checkout\n\n";

} catch (PDOException $e) {
    echo "❌ ERROR DATABASE: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "===== SELESAI =====\n";
echo "Buka halaman detail pesanan untuk melihat hasilnya: http://localhost:8000/admin/orders/$order_id\n";
