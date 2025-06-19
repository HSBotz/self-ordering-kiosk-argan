<?php
/**
 * PERBAIKAN VARIAN HOT/ICE MINUMAN
 *
 * Script ini memperbaiki varian minuman yang belum terisi dengan benar
 * dengan mengatur varian hot/ice berdasarkan aturan sederhana
 */

// Output sebagai HTML
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbaikan Varian Minuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .result-box {
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .hot-badge {
            background-color: #dc3545;
            color: white;
        }
        .ice-badge {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">
            <i class="fas fa-coffee me-2"></i>
            Perbaikan Varian Minuman
        </h1>

        <div class="row">
            <div class="col-lg-8">
                <?php
                // Koneksi ke database
                $host = 'localhost';
                $db = 'kedai_kiosk';
                $user = 'root';
                $pass = '';

                try {
                    // Koneksi ke database
                    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo '<div class="result-box success mb-4"><i class="fas fa-check-circle me-2"></i> Koneksi database berhasil</div>';

                    // 1. Periksa kolom variant_type
                    $columnExists = $pdo->query("SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.COLUMNS
                                                WHERE TABLE_SCHEMA = 'kedai_kiosk'
                                                AND TABLE_NAME = 'order_items'
                                                AND COLUMN_NAME = 'variant_type'")->fetchColumn();

                    echo '<div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-database me-2"></i> Struktur Database</h5>
                            </div>
                            <div class="card-body">';

                    if ($columnExists == 0) {
                        echo '<div class="result-box warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Kolom variant_type tidak ditemukan! Menambahkan kolom...
                            </div>';

                        // Tambahkan kolom
                        $pdo->exec("ALTER TABLE order_items ADD COLUMN variant_type VARCHAR(10) NULL AFTER notes");
                        echo '<div class="result-box success">
                                <i class="fas fa-check-circle me-2"></i> Kolom variant_type berhasil ditambahkan
                            </div>';
                    } else {
                        echo '<div class="result-box success">
                                <i class="fas fa-check-circle me-2"></i> Kolom variant_type sudah tersedia di database
                            </div>';
                    }

                    echo '</div></div>';

                    // 2. Statistik varian sebelum perbaikan
                    $stats = $pdo->query("SELECT
                                        COUNT(*) AS total_items,
                                        SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
                                        SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
                                        SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
                                    FROM order_items")->fetch(PDO::FETCH_ASSOC);

                    echo '<div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Statistik Varian Sebelum Perbaikan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3">' . $stats['total_items'] . '</div>
                                        <div>Total Item</div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-danger">' . $stats['hot_items'] . '</div>
                                        <div><span class="badge hot-badge"><i class="fas fa-fire me-1"></i> Hot</span></div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-info">' . $stats['ice_items'] . '</div>
                                        <div><span class="badge ice-badge"><i class="fas fa-snowflake me-1"></i> Ice</span></div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-secondary">' . $stats['null_items'] . '</div>
                                        <div><span class="badge bg-secondary">Tanpa Varian</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>';

                    // 3. Perbaiki data varian
                    if ($stats['null_items'] > 0) {
                        echo '<div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Perbaikan Data Varian</h5>
                                </div>
                                <div class="card-body">';

                        echo '<div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Ditemukan ' . $stats['null_items'] . ' item tanpa varian!
                            </div>';

                        // Ambil data pesanan yang memiliki item tanpa varian
                        $ordersWithNullVariants = $pdo->query("SELECT DISTINCT o.id, o.order_number
                                                            FROM orders o
                                                            JOIN order_items oi ON o.id = oi.order_id
                                                            WHERE oi.variant_type IS NULL
                                                            ORDER BY o.id DESC")->fetchAll(PDO::FETCH_ASSOC);

                        echo '<p>Pesanan yang perlu diperbaiki:</p>';
                        echo '<ul class="list-group mb-4">';
                        foreach($ordersWithNullVariants as $order) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                    Pesanan #' . $order['order_number'] . ' (ID: ' . $order['id'] . ')
                                    <a href="?fix_order=' . $order['id'] . '" class="btn btn-sm btn-outline-primary">Perbaiki</a>
                                </li>';
                        }
                        echo '</ul>';

                        // Jika ada permintaan perbaikan pesanan tertentu
                        if (isset($_GET['fix_order'])) {
                            $orderId = (int)$_GET['fix_order'];

                            // Perbaiki varian untuk pesanan tertentu
                            $hotCount = $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE order_id = $orderId AND variant_type IS NULL AND id % 2 = 0");
                            $iceCount = $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE order_id = $orderId AND variant_type IS NULL AND id % 2 = 1");

                            echo '<div class="result-box success">
                                    <i class="fas fa-check-circle me-2"></i> Perbaikan untuk pesanan #' . $orderId . ' berhasil:
                                    <ul class="mt-2 mb-0">
                                        <li>' . $hotCount . ' item diatur sebagai HOT</li>
                                        <li>' . $iceCount . ' item diatur sebagai ICE</li>
                                    </ul>
                                </div>';

                            // Tampilkan detail item yang diperbaiki
                            $items = $pdo->query("SELECT oi.id, p.name, oi.variant_type
                                                FROM order_items oi
                                                LEFT JOIN products p ON oi.product_id = p.id
                                                WHERE oi.order_id = $orderId
                                                ORDER BY oi.id")->fetchAll(PDO::FETCH_ASSOC);

                            if (count($items) > 0) {
                                echo '<h6 class="mt-3 mb-2">Detail item pesanan #' . $orderId . ':</h6>';
                                echo '<table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Produk</th>
                                                <th class="text-center">Varian</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                                foreach($items as $item) {
                                    $variantBadge = '';
                                    if ($item['variant_type'] == 'hot') {
                                        $variantBadge = '<span class="badge hot-badge"><i class="fas fa-fire me-1"></i> HOT</span>';
                                    } elseif ($item['variant_type'] == 'ice') {
                                        $variantBadge = '<span class="badge ice-badge"><i class="fas fa-snowflake me-1"></i> ICE</span>';
                                    } else {
                                        $variantBadge = '<span class="badge bg-secondary">-</span>';
                                    }

                                    echo '<tr>
                                            <td>' . $item['id'] . '</td>
                                            <td>' . $item['name'] . '</td>
                                            <td class="text-center">' . $variantBadge . '</td>
                                        </tr>';
                                }

                                echo '</tbody></table>';
                            }
                        }

                        // Tombol perbaikan semua varian
                        echo '<div class="mt-3">
                                <a href="?fix_all=1" class="btn btn-danger" onclick="return confirm(\'Yakin ingin memperbaiki semua varian yang NULL?\')">
                                    <i class="fas fa-magic me-2"></i> Perbaiki Semua Varian
                                </a>
                            </div>';

                        // Jika ada permintaan perbaikan semua
                        if (isset($_GET['fix_all'])) {
                            $hotCount = $pdo->exec("UPDATE order_items SET variant_type = 'hot' WHERE variant_type IS NULL AND id % 2 = 0");
                            $iceCount = $pdo->exec("UPDATE order_items SET variant_type = 'ice' WHERE variant_type IS NULL AND id % 2 = 1");

                            echo '<div class="result-box success mt-3">
                                    <i class="fas fa-check-circle me-2"></i> Perbaikan global berhasil:
                                    <ul class="mt-2 mb-0">
                                        <li>' . $hotCount . ' item diatur sebagai HOT</li>
                                        <li>' . $iceCount . ' item diatur sebagai ICE</li>
                                    </ul>
                                </div>';
                        }

                        echo '</div></div>';
                    }

                    // 4. Statistik varian setelah perbaikan
                    $statsAfter = $pdo->query("SELECT
                                            COUNT(*) AS total_items,
                                            SUM(CASE WHEN variant_type = 'hot' THEN 1 ELSE 0 END) AS hot_items,
                                            SUM(CASE WHEN variant_type = 'ice' THEN 1 ELSE 0 END) AS ice_items,
                                            SUM(CASE WHEN variant_type IS NULL THEN 1 ELSE 0 END) AS null_items
                                        FROM order_items")->fetch(PDO::FETCH_ASSOC);

                    echo '<div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Statistik Varian Setelah Perbaikan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3">' . $statsAfter['total_items'] . '</div>
                                        <div>Total Item</div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-danger">' . $statsAfter['hot_items'] . '</div>
                                        <div><span class="badge hot-badge"><i class="fas fa-fire me-1"></i> Hot</span></div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-info">' . $statsAfter['ice_items'] . '</div>
                                        <div><span class="badge ice-badge"><i class="fas fa-snowflake me-1"></i> Ice</span></div>
                                    </div>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="h3 text-secondary">' . $statsAfter['null_items'] . '</div>
                                        <div><span class="badge bg-secondary">Tanpa Varian</span></div>
                                    </div>
                                </div>';

                    if ($statsAfter['null_items'] == 0) {
                        echo '<div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i> Semua item pesanan memiliki varian yang valid!
                            </div>';
                    }

                    echo '</div></div>';

                } catch (PDOException $e) {
                    echo '<div class="result-box error">
                            <i class="fas fa-exclamation-circle me-2"></i> Error database: ' . $e->getMessage() . '
                        </div>';
                }
                ?>
            </div>

            <div class="col-lg-4">
                <!-- Sidebar -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi</h5>
                    </div>
                    <div class="card-body">
                        <p>Alat ini membantu memperbaiki data varian minuman yang belum terisi dengan benar pada sistem.</p>

                        <div class="alert alert-info">
                            <p class="mb-2"><strong>Varian yang didukung:</strong></p>
                            <div class="mb-2">
                                <span class="badge hot-badge"><i class="fas fa-fire me-1"></i> Hot</span>
                                - Untuk minuman panas
                            </div>
                            <div>
                                <span class="badge ice-badge"><i class="fas fa-snowflake me-1"></i> Ice</span>
                                - Untuk minuman dingin dengan es
                            </div>
                        </div>

                        <hr>

                        <p><strong>Cara kerja perbaikan:</strong></p>
                        <ul>
                            <li>Item dengan ID genap akan diatur sebagai HOT</li>
                            <li>Item dengan ID ganjil akan diatur sebagai ICE</li>
                        </ul>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Pastikan untuk memeriksa ulang hasil perbaikan pada halaman admin.
                        </div>
                    </div>
                </div>

                <!-- Link -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i> Link Berguna</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="http://localhost:8000/admin/orders" target="_blank">
                                    <i class="fas fa-list me-2"></i> Daftar Pesanan
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="http://localhost/phpmyadmin/index.php?route=/sql&db=kedai_kiosk&table=order_items" target="_blank">
                                    <i class="fas fa-database me-2"></i> Data Order Items di phpMyAdmin
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>">
                                    <i class="fas fa-sync me-2"></i> Refresh Halaman
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
