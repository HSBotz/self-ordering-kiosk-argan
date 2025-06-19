<?php
/**
 * DIAGNOSTIK VARIAN HOT/ICE
 * Script ini akan memeriksa status varian di database dan memperbaikinya jika diperlukan.
 */
header('Content-Type: text/html; charset=utf-8');

// Koneksi database
$host = 'localhost';
$db = 'kedai_kiosk';
$user = 'root';
$pass = '';

// Fungsi untuk format output
function echoSuccess($message) {
    echo "<div style='background-color:#d4edda;color:#155724;padding:10px;margin:5px 0;border-radius:3px;'>";
    echo "✅ $message";
    echo "</div>";
}

function echoError($message) {
    echo "<div style='background-color:#f8d7da;color:#721c24;padding:10px;margin:5px 0;border-radius:3px;'>";
    echo "❌ $message";
    echo "</div>";
}

function echoWarning($message) {
    echo "<div style='background-color:#fff3cd;color:#856404;padding:10px;margin:5px 0;border-radius:3px;'>";
    echo "⚠️ $message";
    echo "</div>";
}

function echoSection($title) {
    echo "<h3 style='margin-top:20px;border-bottom:1px solid #ccc;padding-bottom:5px;'>$title</h3>";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostik Varian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        h3 {
            margin-top: 20px;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            overflow: auto;
            font-family: monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .hot {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 3px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
        .ice {
            background-color: #ccf2ff;
            color: #0077cc;
            padding: 3px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
        .null {
            background-color: #f2f2f2;
            color: #777;
            padding: 3px 5px;
            border-radius: 3px;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 5px;
        }
        .btn-danger {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <h1>Diagnostik Varian Minuman Hot/Ice</h1>

    <?php
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echoSuccess("Koneksi database berhasil");

        echoSection("1. Struktur Tabel");

        // Periksa apakah kolom variant_type ada
        $columnExists = $pdo->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS
                                     WHERE TABLE_SCHEMA = '$db'
                                     AND TABLE_NAME = 'order_items'
                                     AND COLUMN_NAME = 'variant_type'")->fetchColumn();

        if ($columnExists == 0) {
            echoError("Kolom variant_type tidak ditemukan di tabel order_items!");

            if (isset($_GET['fix_column'])) {
                // Tambahkan kolom jika diminta
                $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
                echoSuccess("Kolom variant_type telah ditambahkan ke tabel order_items");
            } else {
                echo "<div class='actions'>";
                echo "<a href='?fix_column=1' class='btn'>Tambahkan Kolom variant_type</a>";
                echo "</div>";
            }
        } else {
            // Cek tipe data kolom
            $columnInfo = $pdo->query("SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
                                     FROM INFORMATION_SCHEMA.COLUMNS
                                     WHERE TABLE_SCHEMA = '$db'
                                     AND TABLE_NAME = 'order_items'
                                     AND COLUMN_NAME = 'variant_type'")->fetch(PDO::FETCH_ASSOC);

            $dataType = $columnInfo['DATA_TYPE'];
            $maxLength = $columnInfo['CHARACTER_MAXIMUM_LENGTH'];

            echoSuccess("Kolom variant_type sudah ada dengan tipe data: $dataType($maxLength)");
        }

        // Jika kolom ada atau baru dibuat, periksa data varian
        if ($columnExists > 0 || isset($_GET['fix_column'])) {
            echoSection("2. Statistik Varian");

            $stats = $pdo->query("SELECT
                                COUNT(*) AS total_items,
                                SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
                                SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
                                SUM(CASE WHEN variant_type IS NULL OR variant_type = '' THEN 1 ELSE 0 END) AS null_items
                                FROM order_items")->fetch(PDO::FETCH_ASSOC);

            echo "<table>";
            echo "<tr><th>Total Item</th><th>Hot Items</th><th>Ice Items</th><th>Tanpa Varian</th></tr>";
            echo "<tr>";
            echo "<td>{$stats['total_items']}</td>";
            echo "<td><span class='hot'>{$stats['hot_items']}</span></td>";
            echo "<td><span class='ice'>{$stats['ice_items']}</span></td>";
            echo "<td>{$stats['null_items']}</td>";
            echo "</tr>";
            echo "</table>";

            // Jika ada item tanpa varian, tampilkan opsi perbaikan
            if ($stats['null_items'] > 0) {
                echoWarning("Terdapat {$stats['null_items']} item tanpa varian (null atau string kosong)");

                if (isset($_GET['fix_variants'])) {
                    // Perbaiki varian jika diminta
                    $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE (variant_type IS NULL OR variant_type = '') AND id % 2 = 0");
                    $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE (variant_type IS NULL OR variant_type = '') AND id % 2 = 1");

                    // Ambil statistik terbaru
                    $updatedStats = $pdo->query("SELECT
                                            COUNT(*) AS total_items,
                                            SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
                                            SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
                                            SUM(CASE WHEN variant_type IS NULL OR variant_type = '' THEN 1 ELSE 0 END) AS null_items
                                            FROM order_items")->fetch(PDO::FETCH_ASSOC);

                    echoSuccess("Varian berhasil diperbarui: {$updatedStats['hot_items']} hot, {$updatedStats['ice_items']} ice, {$updatedStats['null_items']} null");
                } else {
                    echo "<div class='actions'>";
                    echo "<a href='?fix_variants=1' class='btn'>Perbaiki Varian Otomatis</a>";
                    echo "</div>";
                }
            } else {
                echoSuccess("Semua item memiliki varian yang valid");
            }

            echoSection("3. Sampel Data");

            // Tampilkan sampel data
            $samples = $pdo->query("SELECT oi.id, oi.order_id, p.name, oi.variant_type
                                  FROM order_items oi
                                  LEFT JOIN products p ON oi.product_id = p.id
                                  ORDER BY oi.order_id DESC, oi.id ASC
                                  LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

            if (count($samples) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Order ID</th><th>Produk</th><th>Varian</th></tr>";

                foreach ($samples as $item) {
                    $variantClass = '';
                    $variantText = $item['variant_type'];

                    if ($item['variant_type'] == 'hot') {
                        $variantClass = 'hot';
                        $variantText = 'HOT 🔥';
                    } elseif ($item['variant_type'] == 'ice') {
                        $variantClass = 'ice';
                        $variantText = 'ICE ❄️';
                    } else {
                        $variantClass = 'null';
                        $variantText = 'NULL';
                    }

                    echo "<tr>";
                    echo "<td>{$item['id']}</td>";
                    echo "<td>{$item['order_id']}</td>";
                    echo "<td>{$item['name']}</td>";
                    echo "<td><span class='$variantClass'>$variantText</span></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echoWarning("Tidak ada data item pesanan di database");
            }
        }

        echoSection("4. Link dan Navigasi");
        echo "<div class='actions'>";
        echo "<a href='http://localhost:8000/admin/orders' class='btn'>Halaman Admin Orders</a>";
        echo "<a href='http://localhost:8000/admin/orders/fix-variants' class='btn'>Perbaiki Semua Varian</a>";
        echo "<a href='?refresh=1' class='btn'>Refresh Halaman</a>";
        echo "</div>";

    } catch (PDOException $e) {
        echoError("Error database: " . $e->getMessage());
    }
    ?>
</body>
</html>
