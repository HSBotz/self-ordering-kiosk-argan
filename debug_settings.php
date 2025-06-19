<?php

// Load Laravel framework
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Header
echo "=== DEBUGGING SITE SETTINGS ===\n";
echo date('Y-m-d H:i:s') . "\n\n";

// Cek database langsung
echo "1. NILAI DARI DATABASE:\n";
$dbSetting = DB::table('site_settings')
    ->where('key', 'footer_social_media_visible')
    ->first();

if ($dbSetting) {
    echo "   - ID: " . $dbSetting->id . "\n";
    echo "   - Key: " . $dbSetting->key . "\n";
    echo "   - Value: '" . $dbSetting->value . "' (tipe: " . gettype($dbSetting->value) . ")\n";
    echo "   - Group: " . $dbSetting->group . "\n";
    echo "   - Type: " . $dbSetting->type . "\n";
    echo "   - Updated at: " . $dbSetting->updated_at . "\n\n";
} else {
    echo "   - Pengaturan tidak ditemukan di database\n\n";
}

// Cek cache
echo "2. NILAI DARI CACHE:\n";
if (Cache::has('footer_settings')) {
    $cachedSettings = Cache::get('footer_settings');
    echo "   - Cache ditemukan\n";

    if (isset($cachedSettings['footer_social_media_visible'])) {
        echo "   - footer_social_media_visible: '" . $cachedSettings['footer_social_media_visible'] . "' (tipe: " .
            gettype($cachedSettings['footer_social_media_visible']) . ")\n\n";
    } else {
        echo "   - footer_social_media_visible tidak ada di cache\n\n";
    }
} else {
    echo "   - Cache footer_settings tidak ditemukan\n\n";
}

// Coba update nilai dan hapus cache untuk testing
echo "3. COBALAH TINDAKAN DEBUG BERIKUT:\n";
echo "   a. Hapus cache dengan perintah: php artisan cache:clear\n";
echo "   b. Perbarui nilai di database secara langsung dengan perintah:\n";
echo "      php artisan tinker --execute=\"DB::table('site_settings')->where('key', 'footer_social_media_visible')->update(['value' => '0']);\"\n";
echo "   c. Verifikasi nilai setelah perbaruan:\n";
echo "      php " . __FILE__ . "\n\n";

echo "=== SELESAI ===\n";
