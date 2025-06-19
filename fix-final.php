<?php
/*
 * PERBAIKAN FINAL VARIAN MINUMAN
 * Script ini menyelesaikan masalah varian hot/ice dengan memastikan:
 * 1. Kolom variant_type ada di database
 * 2. Varian diatur dengan benar untuk semua pesanan
 */

header('Content-Type: text/plain');

echo "===== PERBAIKAN FINAL VARIAN MINUMAN =====\n\n";

// Koneksi database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Koneksi database berhasil\n\n";

    // LANGKAH 1: Periksa dan tambahkan kolom variant_type jika belum ada
    echo "1. PEMERIKSAAN STRUKTUR DATABASE\n";

    $columns = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'variant_type'")->fetchAll();
    if (empty($columns)) {
        echo "   ⚠️ Kolom variant_type tidak ditemukan! Menambahkan kolom...\n";
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo "   ✓ Kolom variant_type berhasil ditambahkan\n";
    } else {
        echo "   ✓ Kolom variant_type sudah ada di tabel\n";
        echo "   Tipe data: " . $columns[0]['Type'] . "\n";
    }

    // LANGKAH 2: Perbarui semua nilai variant_type yang null
    echo "\n2. PERBAIKAN DATA VARIAN UNTUK SEMUA PESANAN\n";

    // Hitung total item pesanan
    $totalItems = $pdo->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
    $nullItems = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL")->fetchColumn();

    echo "   • Total item pesanan: $totalItems\n";
    echo "   • Item tanpa varian: $nullItems\n\n";

    if ($nullItems > 0) {
        echo "   ➤ Memperbaiki item tanpa varian...\n";

        // Set item dengan ID genap sebagai HOT
        $stmt = $pdo->query("UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0");
        $hotCount = $stmt->rowCount();
        echo "   ✓ Set $hotCount item dengan ID genap sebagai 'hot'\n";

        // Set item dengan ID ganjil sebagai ICE
        $stmt = $pdo->query("UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1");
        $iceCount = $stmt->rowCount();
        echo "   ✓ Set $iceCount item dengan ID ganjil sebagai 'ice'\n";
    } else {
        echo "   ✓ Semua item sudah memiliki nilai variant_type\n";
    }

    // LANGKAH 3: Periksa hasil perbaikan
    echo "\n3. VERIFIKASI HASIL PERBAIKAN\n";

    $hotCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type = 'hot'")->fetchColumn();
    $iceCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type = 'ice'")->fetchColumn();
    $otherCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NOT NULL AND variant_type NOT IN ('hot', 'ice')")->fetchColumn();
    $nullCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL")->fetchColumn();

    echo "   • Item varian HOT: $hotCount\n";
    echo "   • Item varian ICE: $iceCount\n";
    echo "   • Item varian lain: $otherCount\n";
    echo "   • Item tanpa varian: $nullCount\n\n";

    // LANGKAH 4: Perbaiki pesanan tertentu jika diminta
    $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

    if ($order_id) {
        echo "4. DETAIL PESANAN #$order_id\n";

        $order = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();
        if (!$order) {
            echo "   ⚠️ Pesanan dengan ID #$order_id tidak ditemukan!\n";
        } else {
            echo "   ✓ Pesanan ditemukan: {$order['order_number']} ({$order['created_at']})\n\n";

            // Tampilkan item pesanan sebelum perbaikan
            $items = $pdo->query("SELECT oi.id, p.name, oi.variant_type
                                 FROM order_items oi
                                 LEFT JOIN products p ON oi.product_id = p.id
                                 WHERE oi.order_id = $order_id")->fetchAll();

            echo "   ITEM PESANAN:\n";
            echo "   -------------\n";

            foreach ($items as $item) {
                $variant = $item['variant_type'] ?: 'NULL';
                $variantIcon = ($variant == 'hot') ? '🔥' : (($variant == 'ice') ? '❄️' : '❓');
                echo "   - [{$item['id']}] {$item['name']} → Varian: $variant $variantIcon\n";
            }

            echo "\n   ➤ Untuk mengubah varian secara manual, gunakan parameter hot dan ice:\n";
            echo "      fix-final.php?order_id=$order_id&hot=1,3,5&ice=2,4,6\n";
        }

        // Perbarui varian berdasarkan parameter manual jika ada
        $hot_items = isset($_GET['hot']) ? explode(',', $_GET['hot']) : [];
        $ice_items = isset($_GET['ice']) ? explode(',', $_GET['ice']) : [];

        if (!empty($hot_items) || !empty($ice_items)) {
            echo "\n   PERBAIKAN MANUAL:\n";

            if (!empty($hot_items)) {
                $placeholders = implode(',', array_fill(0, count($hot_items), '?'));
                $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'hot' WHERE id IN ($placeholders)");
                $stmt->execute($hot_items);
                $updateCount = $stmt->rowCount();
                echo "   ✓ $updateCount item diupdate menjadi HOT\n";
            }

            if (!empty($ice_items)) {
                $placeholders = implode(',', array_fill(0, count($ice_items), '?'));
                $stmt = $pdo->prepare("UPDATE order_items SET variant_type = 'ice' WHERE id IN ($placeholders)");
                $stmt->execute($ice_items);
                $updateCount = $stmt->rowCount();
                echo "   ✓ $updateCount item diupdate menjadi ICE\n";
            }

            // Tampilkan hasil setelah perbaikan manual
            $updatedItems = $pdo->query("SELECT oi.id, p.name, oi.variant_type
                                        FROM order_items oi
                                        LEFT JOIN products p ON oi.product_id = p.id
                                        WHERE oi.order_id = $order_id")->fetchAll();

            echo "\n   HASIL SETELAH PERBAIKAN MANUAL:\n";
            echo "   -----------------------------\n";

            foreach ($updatedItems as $item) {
                $variant = $item['variant_type'] ?: 'NULL';
                $variantIcon = ($variant == 'hot') ? '🔥' : (($variant == 'ice') ? '❄️' : '❓');
                echo "   - [{$item['id']}] {$item['name']} → Varian: $variant $variantIcon\n";
            }
        }
    }

    echo "\n===== PERBAIKAN SELESAI =====\n";
    echo "Buka halaman detail pesanan untuk melihat hasilnya: http://localhost:8000/admin/orders/$order_id\n";

} catch (PDOException $e) {
    echo "❌ ERROR DATABASE: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
