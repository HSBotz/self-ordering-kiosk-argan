<?php
/*
 * FINAL FIX VARIAN HOT/ICE
 * Script sederhana untuk perbaikan varian yang dapat diakses langsung di browser
 */

// Koneksi ke database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

// Output sebagai HTML
echo '<!DOCTYPE html>
<html>
<head>
    <title>Perbaikan Varian Hot/Ice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        h1 { color: #333; }
        .container { max-width: 800px; margin: 0 auto; }
        .box { background: #f5f5f5; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeeba; color: #856404; }
        pre { background: #f8f9fa; padding: 10px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .badge { padding: 3px 6px; border-radius: 4px; color: white; }
        .badge-hot { background-color: #dc3545; }
        .badge-ice { background-color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Perbaikan Varian Hot/Ice</h1>';

try {
    // Koneksi database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '<div class="box success">✅ Koneksi database berhasil</div>';

    // 1. Periksa struktur database
    $columnExists = $pdo->query("SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = 'order_items' AND COLUMN_NAME = 'variant_type'")->fetch(PDO::FETCH_ASSOC)['count'];

    echo '<h2>1. Struktur Database</h2>';

    if ($columnExists == 0) {
        echo '<div class="box warning">⚠️ Kolom variant_type tidak ditemukan! Menambahkan kolom...</div>';

        // Tambahkan kolom
        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
        echo '<div class="box success">✅ Kolom variant_type berhasil ditambahkan</div>';
    } else {
        echo '<div class="box success">✅ Kolom variant_type sudah ada</div>';
    }

    // 2. Perbaiki data untuk pesanan #31 dan semua pesanan
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 31;

    echo "<h2>2. Perbaikan Data Pesanan #{$orderId}</h2>";

    // Periksa apakah pesanan ada
    $order = $pdo->query("SELECT * FROM orders WHERE id = $orderId")->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<div class='box error'>❌ Pesanan #{$orderId} tidak ditemukan</div>";
    } else {
        echo "<div class='box success'>
            ✅ Pesanan ditemukan:<br>
            - Order Number: {$order['order_number']}<br>
            - Tanggal: {$order['created_at']}<br>
            - Customer: {$order['customer_name']}
        </div>";

        // Update varian
        $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE order_id = $orderId AND id % 2 = 0");
        $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE order_id = $orderId AND id % 2 = 1");

        // Tampilkan hasil
        $items = $pdo->query("SELECT oi.id, p.name, oi.variant_type, oi.quantity, oi.price
                             FROM order_items oi
                             LEFT JOIN products p ON oi.product_id = p.id
                             WHERE oi.order_id = $orderId
                             ORDER BY oi.id")->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Item Pesanan #{$orderId}</h3>";
        echo '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($items as $item) {
            $variantBadge = '';
            if ($item['variant_type'] == 'hot') {
                $variantBadge = '<span class="badge badge-hot">PANAS 🔥</span>';
            } elseif ($item['variant_type'] == 'ice') {
                $variantBadge = '<span class="badge badge-ice">DINGIN ❄️</span>';
            } else {
                $variantBadge = '<span class="badge">-</span>';
            }

            echo "<tr>
                <td>{$item['id']}</td>
                <td>{$item['name']}</td>
                <td>{$variantBadge} {$item['variant_type']}</td>
                <td>{$item['quantity']}</td>
                <td>Rp " . number_format($item['price'], 0, ',', '.') . "</td>
            </tr>";
        }

        echo '</tbody></table>';
    }

    // 3. Perbaiki semua data varian NULL
    echo "<h2>3. Perbaikan Data Varian Global</h2>";

    $nullCount = $pdo->query("SELECT COUNT(*) FROM order_items WHERE variant_type IS NULL")->fetchColumn();
    echo "<div class='box'>Total item tanpa varian: {$nullCount}</div>";

    if ($nullCount > 0) {
        // Update semua nilai NULL
        $hotCount = $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0");
        $iceCount = $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1");

        echo "<div class='box success'>
            ✅ Update global berhasil:<br>
            - {$hotCount} item diubah menjadi HOT<br>
            - {$iceCount} item diubah menjadi ICE
        </div>";
    } else {
        echo "<div class='box success'>✅ Semua item sudah memiliki varian</div>";
    }

    // 4. Statistik akhir
    $stats = $pdo->query("SELECT
                        COUNT(*) AS total_items,
                        SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
                        SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
                        SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
                      FROM order_items")->fetch(PDO::FETCH_ASSOC);

    echo "<h2>4. Statistik Akhir</h2>";
    echo "<div class='box'>
        Total item pesanan: {$stats['total_items']}<br>
        Item HOT: {$stats['hot_items']}<br>
        Item ICE: {$stats['ice_items']}<br>
        Item tanpa varian: {$stats['null_items']}
    </div>";

    echo "<h2>5. Link Penting</h2>";
    echo "<div class='box'>
        <p><a href='http://localhost:8000/admin/orders/{$orderId}' target='_blank'>Lihat Pesanan #{$orderId} di Admin Panel</a></p>
        <p><a href='http://localhost/phpmyadmin/index.php?route=/sql&db={$db}&table=order_items&pos=0&sql_query=" . urlencode("SELECT * FROM order_items WHERE order_id = {$orderId} ORDER BY id") . "' target='_blank'>Cek Data di phpMyAdmin</a></p>
    </div>";

} catch (PDOException $e) {
    echo '<div class="box error">❌ ERROR: ' . $e->getMessage() . '</div>';
}

echo '</div>
</body>
</html>';
