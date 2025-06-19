<?php
/**
 * File diagnostik untuk memeriksa masalah dengan halaman pelacakan pesanan
 */

// Konfigurasi error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Waktu mulai
define('DEBUG_START', microtime(true));

// Fungsi untuk menampilkan pesan
function display_message($message, $type = 'info') {
    switch ($type) {
        case 'error':
            echo "<p style='color: red; font-weight: bold;'>[ERROR] $message</p>";
            break;
        case 'success':
            echo "<p style='color: green; font-weight: bold;'>[SUCCESS] $message</p>";
            break;
        case 'warning':
            echo "<p style='color: orange; font-weight: bold;'>[WARNING] $message</p>";
            break;
        default:
            echo "<p>[INFO] $message</p>";
    }
}

// Header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Kedai Coffee Kiosk - Track Order Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2 { color: #333; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Kedai Coffee Kiosk - Track Order Debug</h1>
    <hr>
";

// Cek parameter order number
$orderNumber = $_GET['order'] ?? null;
if (!$orderNumber) {
    display_message("Parameter 'order' tidak ditemukan di URL. Contoh penggunaan: track-debug.php?order=ORD20250617894", "error");
    display_message("URL saat ini: " . $_SERVER['REQUEST_URI']);
    exit("</div></body></html>");
}

display_message("Memeriksa order number: " . $orderNumber);

// Cek apakah file autoload.php ada
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    display_message("File autoload.php tidak ditemukan di: " . $autoloadPath, "error");
    exit("</div></body></html>");
}

// Load autoload.php
try {
    require $autoloadPath;
    display_message("Berhasil load autoload.php", "success");
} catch (Exception $e) {
    display_message("Gagal load autoload.php: " . $e->getMessage(), "error");
    exit("</div></body></html>");
}

// Cek apakah file bootstrap/app.php ada
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    display_message("File bootstrap/app.php tidak ditemukan di: " . $bootstrapPath, "error");
    exit("</div></body></html>");
}

// Load bootstrap/app.php
try {
    $app = require_once $bootstrapPath;
    display_message("Berhasil load bootstrap/app.php", "success");
} catch (Exception $e) {
    display_message("Gagal load bootstrap/app.php: " . $e->getMessage(), "error");
    exit("</div></body></html>");
}

// Cek apakah TrackController ada
$trackControllerPath = __DIR__ . '/../app/Http/Controllers/TrackController.php';
if (!file_exists($trackControllerPath)) {
    display_message("File TrackController.php tidak ditemukan di: " . $trackControllerPath, "error");
} else {
    display_message("File TrackController.php ditemukan", "success");

    // Tampilkan isi file
    display_message("Isi file TrackController.php:");
    echo "<pre>" . htmlspecialchars(file_get_contents($trackControllerPath)) . "</pre>";
}

// Cek apakah view track_order.blade.php ada
$viewPath = __DIR__ . '/../resources/views/kiosk/track_order.blade.php';
if (!file_exists($viewPath)) {
    display_message("File track_order.blade.php tidak ditemukan di: " . $viewPath, "error");
} else {
    display_message("File track_order.blade.php ditemukan", "success");
}

// Cek rute
use Illuminate\Support\Facades\Route;

display_message("Memeriksa rute...");
try {
    $routes = Route::getRoutes();
    $trackRouteFound = false;

    foreach ($routes as $route) {
        if (strpos($route->uri, 'track') !== false) {
            display_message("Rute ditemukan: " . $route->uri . " => " . $route->getActionName(), "success");
            $trackRouteFound = true;
        }
    }

    if (!$trackRouteFound) {
        display_message("Tidak ada rute yang mengandung 'track'", "error");
    }
} catch (Exception $e) {
    display_message("Gagal memeriksa rute: " . $e->getMessage(), "error");
}

// Coba akses database
try {
    $order = \App\Models\Order::where('order_number', $orderNumber)->first();

    if (!$order) {
        display_message("Order dengan nomor {$orderNumber} tidak ditemukan di database", "error");
    } else {
        display_message("Order dengan nomor {$orderNumber} ditemukan di database", "success");
        display_message("Detail order:");
        echo "<pre>";
        print_r($order->toArray());
        echo "</pre>";

        // Cek order items
        $orderItems = $order->orderItems;
        if ($orderItems->isEmpty()) {
            display_message("Order tidak memiliki item", "warning");
        } else {
            display_message("Order memiliki " . $orderItems->count() . " item", "success");
        }
    }
} catch (Exception $e) {
    display_message("Gagal mengakses database: " . $e->getMessage(), "error");
}

// Waktu eksekusi
$executionTime = microtime(true) - DEBUG_START;
display_message("Waktu eksekusi: " . number_format($executionTime, 4) . " detik");

// Footer
echo "</div></body></html>";
