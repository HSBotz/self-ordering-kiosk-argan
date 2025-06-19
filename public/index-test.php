<?php
// File pengujian koneksi ke Laravel

// Konfigurasi error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Waktu mulai aplikasi Laravel
define('LARAVEL_START', microtime(true));

// Path ke file autoload
$autoloadPath = __DIR__ . '/../vendor/autoload.php';

// Path ke bootstrap/app.php
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';

// Informasi debugging
echo "<h1>Pengujian Koneksi Laravel</h1>";
echo "<hr>";

// Cek file autoload
if (file_exists($autoloadPath)) {
    echo "<p style='color: green;'>File autoload.php ditemukan di: $autoloadPath</p>";

    // Coba load autoload
    try {
        require $autoloadPath;
        echo "<p style='color: green;'>Berhasil load autoload.php</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Gagal load autoload.php: " . $e->getMessage() . "</p>";
        die();
    }
} else {
    echo "<p style='color: red;'>File autoload.php tidak ditemukan di: $autoloadPath</p>";
    die();
}

// Cek file bootstrap/app.php
if (file_exists($bootstrapPath)) {
    echo "<p style='color: green;'>File bootstrap/app.php ditemukan di: $bootstrapPath</p>";

    // Coba load bootstrap/app.php
    try {
        $app = require_once $bootstrapPath;
        echo "<p style='color: green;'>Berhasil load bootstrap/app.php</p>";

        // Cek versi Laravel
        if (method_exists($app, 'version')) {
            echo "<p>Laravel Version: " . $app->version() . "</p>";
        }

        // Coba buat kernel
        try {
            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            echo "<p style='color: green;'>Berhasil membuat kernel</p>";

            // Coba handle request
            try {
                $request = Illuminate\Http\Request::capture();
                echo "<p style='color: green;'>Berhasil capture request</p>";

                // Hanya untuk testing, tidak mengirim response
                echo "<p>Test berhasil! Aplikasi Laravel dapat diakses.</p>";
                echo "<p>Jika Anda melihat pesan ini, berarti PHP berjalan dengan benar.</p>";
                echo "<p>Namun jika Anda masih mengalami masalah dengan file index.php, kemungkinan ada masalah dengan konfigurasi server.</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Gagal capture request: " . $e->getMessage() . "</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Gagal membuat kernel: " . $e->getMessage() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Gagal load bootstrap/app.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>File bootstrap/app.php tidak ditemukan di: $bootstrapPath</p>";
}

// Informasi server
echo "<h2>Informasi Server</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
?>
