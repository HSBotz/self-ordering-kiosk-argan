<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Gunakan Bootstrap untuk pagination
        Paginator::useBootstrap();

        // Load settings ke config
        $this->loadSettingsToConfig();

        // Daftarkan observer untuk model-model utama
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);

        try {
            // Periksa apakah tabel site_settings sudah ada
            if (Schema::hasTable('site_settings')) {
                // SELALU hapus cache untuk memastikan data terbaru
                Cache::forget('footer_settings');
                Cache::forget('store_settings');
                Cache::forget('appearance_settings');

                // QUERY LANGSUNG ke database untuk memastikan data terbaru
                // 1. Pengaturan Footer
                $footerData = [];
                $footerSettings = DB::table('site_settings')->where('group', 'footer')->get();

                foreach ($footerSettings as $setting) {
                    $footerData[$setting->key] = $setting->value;
                }

                // Khusus untuk footer_social_media_visible, ambil langsung dari database
                $socialMediaVisible = DB::table('site_settings')
                    ->where('key', 'footer_social_media_visible')
                    ->value('value');

                // Log nilai yang diambil langsung dari database
                Log::info("AppServiceProvider: footer_social_media_visible diambil langsung dari database = '{$socialMediaVisible}'");

                // Set nilai ke dalam footerData
                $footerData['footer_social_media_visible'] = $socialMediaVisible ?? '1';

                // Log nilai yang akan digunakan
                Log::info("AppServiceProvider: footer_social_media_visible yang akan digunakan = '{$footerData['footer_social_media_visible']}'");

                // 2. Pengaturan Toko
                $storeData = [];
                $storeSettings = DB::table('site_settings')->where('group', 'store')->get();

                // Tampilkan log untuk debugging
                Log::info('AppServiceProvider: Mengambil store settings dari database', [
                    'count' => $storeSettings->count(),
                    'data' => $storeSettings->pluck('value', 'key')
                ]);

                foreach ($storeSettings as $setting) {
                    $storeData[$setting->key] = $setting->value;
                    Log::info("AppServiceProvider: Store setting loaded - {$setting->key} = {$setting->value}");
                }

                // 3. Pengaturan Tampilan
                $appearanceData = [];
                $appearanceSettings = DB::table('site_settings')->where('group', 'appearance')->get();

                foreach ($appearanceSettings as $setting) {
                    $appearanceData[$setting->key] = $setting->value;
                }

                // Bagikan ke semua view
                View::share('footerSettings', $footerData);
                View::share('storeSettings', $storeData);
                View::share('appearanceSettings', $appearanceData);

                // Log untuk memastikan nilai yang digunakan
                Log::info("View shared footerData: " . json_encode($footerData));
                Log::info("View shared storeData: " . json_encode($storeData));
                Log::info("View shared appearanceData: " . json_encode($appearanceData));
            } else {
                // Jika tabel belum ada, gunakan nilai default
                $this->useDefaultSettings();
            }
        } catch (QueryException $e) {
            // Jika terjadi error database, gunakan nilai default
            $this->useDefaultSettings($e->getMessage());
        }

        // Mendaftarkan view composer untuk pengaturan footer
        View::composer('*', function ($view) {
            try {
                // Ambil pengaturan footer dari cache, atau fetch dari database bila tidak ada
                $footerSettings = Cache::remember('footer_settings', 86400, function () {
                    return DB::table('site_settings')
                            ->where('group', 'footer')
                            ->pluck('value', 'key')
                            ->toArray();
                });

                // Tambahkan log untuk debug
                Log::debug("AppServiceProvider: Footer settings fetched", ['count' => count($footerSettings), 'social_visible' => $footerSettings['footer_social_media_visible'] ?? 'not_set']);

                // Share data footer ke view
                $view->with('footerSettings', $footerSettings);
            } catch (\Exception $e) {
                Log::error("Error loading footer settings: " . $e->getMessage());
                // Berikan array kosong untuk menghindari error pada view
                $view->with('footerSettings', []);
            }
        });

        // Mendaftarkan view composer untuk pengaturan toko
        View::composer('*', function ($view) {
            try {
                // Ambil pengaturan toko dari cache, atau fetch dari database bila tidak ada
                $storeSettings = Cache::remember('store_settings', 86400, function () {
                    return DB::table('site_settings')
                            ->where('group', 'store')
                            ->pluck('value', 'key')
                            ->toArray();
                });

                // Share data toko ke view
                $view->with('storeSettings', $storeSettings);
            } catch (\Exception $e) {
                Log::error("Error loading store settings: " . $e->getMessage());
                // Berikan array kosong untuk menghindari error pada view
                $view->with('storeSettings', []);
            }
        });

        // Mendaftarkan view composer untuk pengaturan tampilan
        View::composer('*', function ($view) {
            try {
                // Ambil pengaturan tampilan dari cache, atau fetch dari database bila tidak ada
                $appearanceSettings = Cache::remember('appearance_settings', 86400, function () {
                    return DB::table('site_settings')
                            ->where('group', 'appearance')
                            ->pluck('value', 'key')
                            ->toArray();
                });

                // Share data tampilan ke view
                $view->with('appearanceSettings', $appearanceSettings);
            } catch (\Exception $e) {
                Log::error("Error loading appearance settings: " . $e->getMessage());
                // Berikan array kosong untuk menghindari error pada view
                $view->with('appearanceSettings', []);
            }
        });

        // Mendaftarkan view composer untuk pengaturan pembayaran
        View::composer('*', function ($view) {
            try {
                // Ambil pengaturan pembayaran dari cache, atau fetch dari database bila tidak ada
                $paymentSettings = Cache::remember('payment_settings', 86400, function () {
                    return DB::table('site_settings')
                            ->where('group', 'payment')
                            ->pluck('value', 'key')
                            ->toArray();
                });

                // Share data pembayaran ke view
                $view->with('paymentSettings', $paymentSettings);
            } catch (\Exception $e) {
                Log::error("Error loading payment settings: " . $e->getMessage());
                // Berikan array kosong untuk menghindari error pada view
                $view->with('paymentSettings', []);
            }
        });

        // Solusi untuk masalah 404 pada rute pelacakan pesanan
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('http');
        }

        // Pastikan angka desimal diformat menggunakan koma sebagai pemisah (standar Indonesia)
        \Illuminate\Support\Facades\Blade::directive('rupiah', function ($amount) {
            return "<?php echo 'Rp ' . number_format($amount, 0, ',', '.'); ?>";
        });
    }

    /**
     * Gunakan pengaturan default untuk footer jika terjadi masalah
     *
     * @param string|null $errorMessage
     * @return void
     */
    private function useDefaultSettings($errorMessage = null)
    {
        $footerData = [
            'footer_about_title' => 'Kedai Coffee Kiosk',
            'footer_about_text' => 'Nikmati kopi premium kami dengan layanan self-ordering yang mudah dan cepat.',
            'footer_social_facebook' => '#',
            'footer_social_instagram' => '#',
            'footer_social_twitter' => '#',
            'footer_social_media_visible' => '1',
            'footer_hours_title' => 'Jam Buka',
            'footer_hours_weekday' => 'Senin - Jumat: 08:00 - 22:00',
            'footer_hours_weekend' => 'Sabtu - Minggu: 09:00 - 23:00',
            'footer_contact_title' => 'Kontak',
            'footer_contact_address' => 'Jl. Kopi No. 123, Kota',
            'footer_contact_phone' => '+62 123 4567 890',
            'footer_contact_email' => 'info@kedaicoffee.com',
            'footer_copyright' => 'Kedai Coffee Kiosk. Semua hak dilindungi.'
        ];

        // Data default untuk store
        $storeData = [
            'store_name' => 'Kedai Coffee',
            'store_header_name' => 'Kedai Coffee Kiosk',
            'store_description' => 'Nikmati berbagai pilihan kopi premium dan makanan lezat. Pesan dengan mudah melalui kiosk kami.',
            'store_tagline' => 'Sajian kopi pilihan berkualitas',
            'store_logo' => '',
            'store_phone' => '+62 123 4567 890',
            'store_email' => 'info@kedaicoffee.com',
            'store_address' => 'Jl. Kopi No. 123',
            'store_city' => 'Jakarta',
            'store_province' => 'DKI Jakarta',
            'store_postal_code' => '12345',
            'store_currency' => 'IDR',
            'store_tax_percentage' => '10',
        ];

        // Data default untuk appearance
        $appearanceData = [
            'appearance_primary_color' => '#6a3412',
            'appearance_secondary_color' => '#a87d56',
            'appearance_accent_color' => '#ffb74d',
            'appearance_text_color' => '#333333',
            'appearance_heading_font' => 'Poppins',
            'appearance_body_font' => 'Poppins',
        ];

        if ($errorMessage) {
            Log::error('Error loading site settings: ' . $errorMessage);
        }

        View::share('footerSettings', $footerData);
        View::share('storeSettings', $storeData);
        View::share('appearanceSettings', $appearanceData);

        // Log untuk debugging
        Log::info("AppServiceProvider: Loaded default settings karena tabel belum ada atau terjadi kesalahan");
        Log::info("AppServiceProvider: Default store settings:", $storeData);
    }

    /**
     * Load settings dari database ke config global
     *
     * @return void
     */
    private function loadSettingsToConfig()
    {
        try {
            // Periksa apakah tabel site_settings sudah ada
            if (Schema::hasTable('site_settings')) {
                // Ambil semua pengaturan yang diperlukan
                $storeSettings = DB::table('site_settings')
                    ->where('group', 'store')
                    ->pluck('value', 'key')
                    ->toArray();

                // Set ke konfigurasi
                config(['settings' => $storeSettings]);

                // Log untuk debugging
                Log::info('Settings loaded to config', ['count' => count($storeSettings)]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading settings to config: ' . $e->getMessage());
        }
    }
}
