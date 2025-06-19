<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteSettingController extends Controller
{
    /**
     * Display the site settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Group settings by group
        $settings = SiteSetting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Show the footer settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function footer()
    {
        $footerSettings = SiteSetting::where('group', 'footer')->get();

        // Convert to key-value array for easier access in view
        $settings = [];
        foreach ($footerSettings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admin.settings.footer', compact('settings', 'footerSettings'));
    }

    /**
     * Update the footer settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFooter(Request $request)
    {
        // Validate the request
        $request->validate([
            'footer_about_title' => 'required|string|max:255',
            'footer_about_text' => 'required|string',
            'footer_social_facebook' => 'nullable|string|max:255',
            'footer_social_instagram' => 'nullable|string|max:255',
            'footer_social_twitter' => 'nullable|string|max:255',
            'footer_social_media_visible' => 'required|in:0,1',
            'footer_hours_title' => 'required|string|max:255',
            'footer_hours_weekday' => 'required|string|max:255',
            'footer_hours_weekend' => 'required|string|max:255',
            'footer_contact_title' => 'required|string|max:255',
            'footer_contact_address' => 'required|string|max:255',
            'footer_contact_phone' => 'required|string|max:255',
            'footer_contact_email' => 'required|email|max:255',
            'footer_copyright' => 'required|string|max:255',
        ]);

        // Hapus cache terlebih dahulu
        Cache::forget('footer_settings');
        Log::info('Footer settings cache cleared');

        // Debugging - log semua data yang diterima
        Log::info('Footer settings update request data:', $request->all());
        Log::info('Nilai footer_social_media_visible: ' . $request->input('footer_social_media_visible'));

        try {
            // Update semua pengaturan dalam satu transaksi database
            DB::beginTransaction();

            // Pastikan nilai footer_social_media_visible disimpan dengan benar
            $socialMediaVisible = (string) $request->input('footer_social_media_visible');
            $socialMediaSetting = SiteSetting::where('key', 'footer_social_media_visible')->first();

            if ($socialMediaSetting) {
                $socialMediaSetting->value = $socialMediaVisible;
                $saved = $socialMediaSetting->save();
                Log::info("Updating footer_social_media_visible directly: " . ($saved ? 'success' : 'failed') . ", value: {$socialMediaVisible}");
            } else {
                $socialMediaSetting = SiteSetting::create([
                    'key' => 'footer_social_media_visible',
                    'value' => $socialMediaVisible,
                    'group' => 'footer',
                    'type' => 'boolean',
                    'description' => 'Apakah media sosial ditampilkan di footer'
                ]);
                Log::info("Creating footer_social_media_visible: " . ($socialMediaSetting ? 'success' : 'failed') . ", value: {$socialMediaVisible}");
            }

            // Update pengaturan lainnya
            foreach ($request->except('_token', '_method', 'footer_social_media_visible') as $key => $value) {
                $updated = SiteSetting::updateValue($key, $value);
                Log::info("Updating {$key} with value {$value}: " . ($updated ? 'success' : 'failed'));
            }

            DB::commit();

            // Verifikasi bahwa nilai telah disimpan dengan benar
            $verifiedSetting = SiteSetting::where('key', 'footer_social_media_visible')->first();
            Log::info("Verifikasi nilai footer_social_media_visible setelah update: " . ($verifiedSetting ? $verifiedSetting->value : 'not found'));

            // Pastikan cache telah dihapus
            if (Cache::has('footer_settings')) {
                Cache::forget('footer_settings');
                Log::info('Footer settings cache cleared again after update');
            }

            return redirect()->route('admin.settings.footer')->with('success', 'Pengaturan footer berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating footer settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan. Silakan coba lagi.');
        }
    }

    /**
     * Update multiple settings at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        // Update each setting
        foreach ($request->except('_token', '_method') as $key => $value) {
            SiteSetting::updateValue($key, $value);
        }

        // Bersihkan cache jika ada pengaturan footer yang diubah
        if (strpos(implode(',', array_keys($request->except('_token', '_method'))), 'footer_') !== false) {
            Cache::forget('footer_settings');
            Log::info('Footer settings cache cleared from updateSettings');
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Show the store settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $storeSettings = SiteSetting::where('group', 'store')->get();

        // Convert to key-value array for easier access in view
        $settings = [];
        foreach ($storeSettings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admin.settings.store', compact('settings'));
    }

    /**
     * Update the store settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_header_name' => 'required|string|max:255',
            'store_description' => 'required|string',
            'store_tagline' => 'required|string|max:255',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:204800',
            'logo_option' => 'required|string|in:icon_coffee,icon_mug,icon_store,icon_utensils,custom,none',
            'admin_sidebar_title' => 'nullable|string|max:255',
            'admin_sidebar_subtitle' => 'nullable|string|max:255',
            'store_timezone' => 'required|string|in:WIB,WITA,WIT',
            'show_qr_code' => 'nullable|in:0,1',
            'qr_code_size' => 'nullable|string|in:small,medium,large',
        ]);

        // Log for debugging
        Log::info('Store settings update request data:', $request->all());
        Log::info('Logo option selected: ' . $request->input('logo_option'));

        try {
            // Update semua pengaturan dalam satu transaksi database
            DB::beginTransaction();

            // Handle logo berdasarkan opsi yang dipilih
            if ($request->input('logo_option') !== 'custom') {
                // Jika opsi bukan custom, gunakan nilai logo_option sebagai nilai store_logo
                if ($request->input('logo_option') === 'none') {
                    // Jika none, set logo menjadi kosong
                    $this->updateOrCreateStoreSetting('store_logo', '');
                } else {
                    // Jika icon bawaan, gunakan nilai logo_option
                    $this->updateOrCreateStoreSetting('store_logo', $request->input('logo_option'));
                }

                // Hapus file logo kustom yang mungkin ada sebelumnya
                $oldLogoSetting = SiteSetting::where('key', 'store_logo')->first();
                if ($oldLogoSetting && !empty($oldLogoSetting->value) &&
                    !in_array($oldLogoSetting->value, ['icon_coffee', 'icon_mug', 'icon_store', 'icon_utensils', 'none', '']) &&
                    file_exists(public_path($oldLogoSetting->value))) {
                    unlink(public_path($oldLogoSetting->value));
                    Log::info("Menghapus file logo kustom lama: {$oldLogoSetting->value}");
                }
            } else {
                // Jika custom dan file diunggah
                if ($request->hasFile('store_logo')) {
                    $file = $request->file('store_logo');
                    $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = 'uploads/store';

                    // Pastikan direktori ada
                    if (!file_exists(public_path($path))) {
                        mkdir(public_path($path), 0755, true);
                    }

                    // Hapus logo lama jika ada dan bukan icon bawaan
                    $oldLogoSetting = SiteSetting::where('key', 'store_logo')->first();
                    if ($oldLogoSetting && !empty($oldLogoSetting->value) &&
                        !in_array($oldLogoSetting->value, ['icon_coffee', 'icon_mug', 'icon_store', 'icon_utensils', 'none', '']) &&
                        file_exists(public_path($oldLogoSetting->value))) {
                        unlink(public_path($oldLogoSetting->value));
                        Log::info("Menghapus file logo kustom lama: {$oldLogoSetting->value}");
                    }

                    // Pindahkan file
                    $file->move(public_path($path), $filename);

                    // Simpan path ke database - pastikan menggunakan grup store
                    $this->updateOrCreateStoreSetting('store_logo', $path . '/' . $filename);
                    Log::info("Uploaded new store logo: {$path}/{$filename}");
                } else {
                    // Jika tidak ada file baru yang diunggah tapi opsi custom dipilih, pertahankan logo kustom yang ada
                    Log::info("Logo opsi custom dipilih, tapi tidak ada file baru diunggah. Mempertahankan logo yang ada.");
                }
            }

            // Update pengaturan nama dan deskripsi toko
            $settingsToUpdate = [
                'store_name' => $request->input('store_name'),
                'store_header_name' => $request->input('store_header_name'),
                'store_description' => $request->input('store_description'),
                'store_tagline' => $request->input('store_tagline'),
                'admin_sidebar_title' => $request->input('admin_sidebar_title'),
                'admin_sidebar_subtitle' => $request->input('admin_sidebar_subtitle'),
                'store_timezone' => $request->input('store_timezone'),
            ];

            // Update pengaturan QR code
            $settingsToUpdate['show_qr_code'] = $request->has('show_qr_code') ? '1' : '0';
            $settingsToUpdate['qr_code_size'] = $request->input('qr_code_size', 'medium');

            foreach ($settingsToUpdate as $key => $value) {
                $this->updateOrCreateStoreSetting($key, $value);
            }

            // Pastikan cache pengaturan dibersihkan
            Cache::forget('settings');
            Cache::forget('store_settings');
            Cache::forget('setting_show_qr_code');
            Cache::forget('setting_qr_code_size');

            DB::commit();
            return redirect()->route('admin.settings.store')->with('success', 'Pengaturan toko berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating store settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan. Silakan coba lagi.');
        }
    }

    /**
     * Update or create a store setting.
     *
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @return \App\Models\SiteSetting
     */
    private function updateOrCreateStoreSetting($key, $value)
    {
        try {
            // Pastikan value adalah string dan bukan null
            $stringValue = (string)($value ?? '');

            Log::info("SiteSettingController::updateOrCreateStoreSetting - Mencoba menyimpan {$key} = '{$stringValue}'");

            // Bersihkan cache terlebih dahulu
            Cache::forget('setting_' . $key);
            Cache::forget('settings_group_store');
            Cache::forget('store_settings');

            // Cari pengaturan yang sudah ada
            $setting = SiteSetting::where('key', $key)->first();

            if (!$setting) {
                // Jika setting belum ada, buat baru dengan grup 'store'
                Log::info("SiteSettingController::updateOrCreateStoreSetting - Membuat pengaturan baru untuk {$key}");

                $setting = new SiteSetting();
                $setting->key = $key;
                $setting->value = $stringValue;
                $setting->group = 'store';
                $setting->type = $this->getSettingType($key);
                $setting->description = 'Pengaturan toko - ' . $key;
                $success = $setting->save();

                if ($success) {
                    Log::info("SiteSettingController::updateOrCreateStoreSetting - Berhasil membuat pengaturan baru {$key} = '{$stringValue}'");
                } else {
                    Log::error("SiteSettingController::updateOrCreateStoreSetting - Gagal membuat pengaturan baru {$key}");
                    return false;
                }
            } else {
                // Jika setting sudah ada, perbarui nilai dan pastikan grup benar
                Log::info("SiteSettingController::updateOrCreateStoreSetting - Memperbarui pengaturan {$key} dari '{$setting->value}' menjadi '{$stringValue}'");

                $setting->value = $stringValue;
                $setting->group = 'store';  // Pastikan grup selalu 'store'
                $success = $setting->save();

                if ($success) {
                    Log::info("SiteSettingController::updateOrCreateStoreSetting - Berhasil memperbarui pengaturan {$key} = '{$stringValue}'");
                } else {
                    Log::error("SiteSettingController::updateOrCreateStoreSetting - Gagal memperbarui pengaturan {$key}");
                    return false;
                }
            }

            // Verifikasi dengan query langsung
            $verifiedSetting = SiteSetting::where('key', $key)->first();
            if ($verifiedSetting) {
                Log::info("SiteSettingController::updateOrCreateStoreSetting - Verifikasi {$key}: nilai tersimpan = '{$verifiedSetting->value}'");

                // Bersihkan cache lagi untuk memastikan
                Cache::forget('setting_' . $key);
                Cache::forget('settings_group_store');
                Cache::forget('store_settings');
            } else {
                Log::error("SiteSettingController::updateOrCreateStoreSetting - KESALAHAN: Setting tidak ditemukan setelah disimpan");
            }

            return $setting;
        } catch (\Exception $e) {
            Log::error("SiteSettingController::updateOrCreateStoreSetting - Kesalahan: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Show the appearance settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function appearance()
    {
        $appearanceSettings = SiteSetting::where('group', 'appearance')->get();

        // Convert to key-value array for easier access in view
        $settings = [];
        foreach ($appearanceSettings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admin.settings.appearance', compact('settings'));
    }

    /**
     * Update the appearance settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAppearance(Request $request)
    {
        // Validate the request
        $request->validate([
            'appearance_primary_color' => 'required|string|starts_with:#|size:7',
            'appearance_secondary_color' => 'required|string|starts_with:#|size:7',
            'appearance_accent_color' => 'required|string|starts_with:#|size:7',
            'appearance_text_color' => 'required|string|starts_with:#|size:7',
            'appearance_heading_font' => 'required|string|max:50',
            'appearance_body_font' => 'required|string|max:50',
        ]);

        // Log for debugging
        Log::info('Appearance settings update request data:', $request->all());

        try {
            // Update semua pengaturan dalam satu transaksi database
            DB::beginTransaction();

            // Update pengaturan tampilan
            foreach ($request->except('_token', '_method') as $key => $value) {
                $updated = SiteSetting::updateValue($key, $value);
                Log::info("Updating {$key} with value {$value}: " . ($updated ? 'success' : 'failed'));
            }

            DB::commit();

            // Hapus cache terkait jika ada
            Cache::forget('appearance_settings');

            return redirect()->route('admin.settings.appearance')->with('success', 'Pengaturan tampilan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating appearance settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan tampilan. Silakan coba lagi.');
        }
    }

    /**
     * Show the payment settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        $paymentSettings = SiteSetting::where('group', 'payment')->get();

        // Convert to key-value array for easier access in view
        $settings = [];
        foreach ($paymentSettings as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        // Jika tidak ada gambar QRIS, buat gambar QRIS default untuk pengujian
        $qrisSetting = SiteSetting::where('key', 'payment_qris_image')->first();
        if ($qrisSetting && empty($qrisSetting->value)) {
            $defaultQrisPath = 'uploads/payment/default_qris.png';
            $qrisFullPath = public_path($defaultQrisPath);

            // Pastikan direktori ada
            if (!file_exists(public_path('uploads/payment'))) {
                mkdir(public_path('uploads/payment'), 0755, true);
            }

            // Buat gambar QRIS contoh jika belum ada
            if (!file_exists($qrisFullPath)) {
                // Buat gambar 300x300 dengan warna background putih
                $image = imagecreatetruecolor(300, 300);
                $white = imagecolorallocate($image, 255, 255, 255);
                $black = imagecolorallocate($image, 0, 0, 0);

                // Isi background putih
                imagefill($image, 0, 0, $white);

                // Buat bingkai hitam
                imagerectangle($image, 10, 10, 290, 290, $black);

                // Tulis teks QRIS di tengah
                $text = "QRIS";
                $font = 5; // Font bawaan GD
                $textWidth = imagefontwidth($font) * strlen($text);
                $textHeight = imagefontheight($font);

                imagestring($image, $font, (300 - $textWidth) / 2, 120, $text, $black);
                imagestring($image, 3, (300 - 180) / 2, 150, "Scan Untuk Pembayaran", $black);

                // Simpan gambar
                imagepng($image, $qrisFullPath);
                imagedestroy($image);

                // Update setting di database
                $qrisSetting->value = $defaultQrisPath;
                $qrisSetting->save();
                Log::info("Created default QRIS image for testing");
            }
        }

        return view('admin.settings.payment', compact('settings'));
    }

    /**
     * Update the payment settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'payment_tax_percentage' => 'required|numeric|min:0|max:100',
            'payment_currency' => 'required|string|max:10',
            'payment_decimal_separator' => 'required|in:.,',
            'payment_thousand_separator' => 'required|in:.,space,none',
            'payment_currency_position' => 'required|in:before,after',
            'payment_qris_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:204800',
            'payment_debit_cards' => 'nullable|string',
        ]);

        // Log for debugging
        Log::info('Payment settings update request data:', $request->all());

        try {
            // Update semua pengaturan dalam satu transaksi database
            DB::beginTransaction();

            // Proses logo QRIS jika diunggah
            if ($request->hasFile('payment_qris_image')) {
                $file = $request->file('payment_qris_image');
                $filename = 'qris_' . time() . '.' . $file->getClientOriginalExtension();
                $path = 'uploads/payment';

                // Pastikan direktori ada
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0755, true);
                }

                // Hapus logo lama jika ada
                $oldQrisImageSetting = SiteSetting::where('key', 'payment_qris_image')->first();
                if ($oldQrisImageSetting && !empty($oldQrisImageSetting->value) && file_exists(public_path($oldQrisImageSetting->value))) {
                    unlink(public_path($oldQrisImageSetting->value));
                }

                // Pindahkan file
                $file->move(public_path($path), $filename);

                // Simpan path ke database
                $this->updateOrCreatePaymentSetting('payment_qris_image', $path . '/' . $filename);
                Log::info("Uploaded new QRIS image: {$path}/{$filename}");
            }

            // Jika checkbox remove_qris_image dicentang, hapus gambar QRIS
            if ($request->has('remove_qris_image') && $request->input('remove_qris_image') == '1') {
                $qrisImageSetting = SiteSetting::where('key', 'payment_qris_image')->first();
                if ($qrisImageSetting && !empty($qrisImageSetting->value)) {
                    // Hapus file fisik jika ada
                    if (file_exists(public_path($qrisImageSetting->value))) {
                        unlink(public_path($qrisImageSetting->value));
                    }

                    // Hapus nilai dari database
                    $qrisImageSetting->value = '';
                    $qrisImageSetting->save();
                    Log::info("Removed QRIS image");
                }
            }

            // Update pengaturan pembayaran normal
            $standardSettings = [
                'payment_tax_percentage',
                'payment_currency',
                'payment_decimal_separator',
                'payment_thousand_separator',
                'payment_currency_position',
                'payment_debit_cards',
            ];

            foreach ($standardSettings as $key) {
                if ($request->has($key)) {
                    $this->updateOrCreatePaymentSetting($key, $request->input($key));
                    Log::info("Updating {$key} dengan value {$request->input($key)}");
                }
            }

            // Khusus untuk checkboxes metode pembayaran, perlu handling ketika tidak dicentang
            // Jika dicentang, nilai akan ada di request dan = '1', jika tidak akan tidak ada di request
            $this->updateOrCreatePaymentSetting('payment_cash_enabled', $request->has('payment_cash_enabled') ? '1' : '0');
            Log::info("Updating payment_cash_enabled dengan value " . ($request->has('payment_cash_enabled') ? '1' : '0'));

            $this->updateOrCreatePaymentSetting('payment_qris_enabled', $request->has('payment_qris_enabled') ? '1' : '0');
            Log::info("Updating payment_qris_enabled dengan value " . ($request->has('payment_qris_enabled') ? '1' : '0'));

            $this->updateOrCreatePaymentSetting('payment_debit_enabled', $request->has('payment_debit_enabled') ? '1' : '0');
            Log::info("Updating payment_debit_enabled dengan value " . ($request->has('payment_debit_enabled') ? '1' : '0'));

            DB::commit();

            // Hapus cache terkait jika ada
            Cache::forget('payment_settings');

            return redirect()->route('admin.settings.payment')->with('success', 'Pengaturan pembayaran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Setup default payment settings (temporary method)
     *
     * @return \Illuminate\Http\Response
     */
    public function setupPaymentSettings()
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            $settings = [
                'payment_tax_percentage' => '10',
                'payment_currency' => 'Rp',
                'payment_decimal_separator' => '.',
                'payment_thousand_separator' => '.',
                'payment_currency_position' => 'before',
                'payment_cash_enabled' => '1',
                'payment_qris_enabled' => '0',
                'payment_qris_image' => '',
                'payment_debit_enabled' => '0',
                'payment_debit_cards' => 'Visa, Mastercard, JCB, American Express',
            ];

            // Simpan semua pengaturan ke database
            foreach ($settings as $key => $value) {
                $this->updateOrCreatePaymentSetting($key, $value);
                Log::info("Setup payment setting: {$key} = {$value}");
            }

            // Default QRIS image
            $qrisSetting = SiteSetting::where('key', 'payment_qris_image')->first();
            if ($qrisSetting && empty($qrisSetting->value)) {
                $defaultQrisPath = 'uploads/payment/default_qris.png';
                $qrisFullPath = public_path($defaultQrisPath);

                // Pastikan direktori ada
                if (!file_exists(public_path('uploads/payment'))) {
                    mkdir(public_path('uploads/payment'), 0755, true);
                }

                // Buat gambar QRIS contoh jika belum ada
                if (!file_exists($qrisFullPath)) {
                    // Buat gambar 300x300 dengan warna background putih
                    $image = imagecreatetruecolor(300, 300);
                    $white = imagecolorallocate($image, 255, 255, 255);
                    $black = imagecolorallocate($image, 0, 0, 0);

                    // Isi background putih
                    imagefill($image, 0, 0, $white);

                    // Buat bingkai hitam
                    imagerectangle($image, 10, 10, 290, 290, $black);

                    // Tulis teks QRIS di tengah
                    $text = "QRIS";
                    $font = 5; // Font bawaan GD
                    $textWidth = imagefontwidth($font) * strlen($text);
                    $textHeight = imagefontheight($font);

                    imagestring($image, $font, (300 - $textWidth) / 2, 120, $text, $black);
                    imagestring($image, 3, (300 - 180) / 2, 150, "Scan Untuk Pembayaran", $black);

                    // Simpan gambar
                    imagepng($image, $qrisFullPath);
                    imagedestroy($image);

                    // Update setting di database
                    $qrisSetting->value = $defaultQrisPath;
                    $qrisSetting->save();
                    Log::info("Created default QRIS image for testing");
                }
            }

            DB::commit();

            // Redirect ke halaman payment settings dengan pesan sukses
            return redirect()->route('admin.settings.payment')->with('success', 'Pengaturan pembayaran berhasil diinisialisasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error setting up payment settings: ' . $e->getMessage());
            return redirect()->route('admin.settings.payment')->with('error', 'Terjadi kesalahan saat menyiapkan pengaturan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk memperbarui atau membuat pengaturan pembayaran
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    private function updateOrCreatePaymentSetting($key, $value)
    {
        try {
            // Pastikan value adalah string dan bukan null
            $stringValue = (string)($value ?? '');

            Log::info("SiteSettingController::updateOrCreatePaymentSetting - Mencoba menyimpan {$key} = '{$stringValue}'");

            // Cari pengaturan yang sudah ada
            $setting = DB::table('site_settings')->where('key', $key)->first();

            if (!$setting) {
                // Jika setting belum ada, buat baru dengan grup 'payment'
                Log::info("SiteSettingController::updateOrCreatePaymentSetting - Membuat pengaturan baru untuk {$key}");
                $inserted = DB::table('site_settings')->insert([
                    'key' => $key,
                    'value' => $stringValue,
                    'group' => 'payment',
                    'type' => $this->getSettingType($key),
                    'description' => 'Pengaturan pembayaran - ' . $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($inserted) {
                    Log::info("SiteSettingController::updateOrCreatePaymentSetting - Berhasil membuat pengaturan baru {$key}");
                } else {
                    Log::error("SiteSettingController::updateOrCreatePaymentSetting - Gagal membuat pengaturan baru {$key}");
                    return false;
                }
            } else {
                // Jika setting sudah ada, perbarui nilai dan pastikan grup benar
                Log::info("SiteSettingController::updateOrCreatePaymentSetting - Memperbarui pengaturan {$key} dari '{$setting->value}' menjadi '{$stringValue}'");
                $updated = DB::table('site_settings')
                    ->where('key', $key)
                    ->update([
                        'value' => $stringValue,
                        'group' => 'payment',  // Pastikan grup selalu 'payment'
                        'updated_at' => now()
                    ]);

                if ($updated) {
                    Log::info("SiteSettingController::updateOrCreatePaymentSetting - Berhasil memperbarui pengaturan {$key}");
                } else {
                    Log::error("SiteSettingController::updateOrCreatePaymentSetting - Gagal memperbarui pengaturan {$key}");
                    return false;
                }
            }

            // Force hapus cache
            Cache::forget('payment_settings');

            // Verifikasi dengan query langsung
            $verifiedValue = DB::table('site_settings')->where('key', $key)->value('value');
            Log::info("SiteSettingController::updateOrCreatePaymentSetting - Verifikasi {$key}: nilai tersimpan = '{$verifiedValue}'");

            return true;
        } catch (\Exception $e) {
            Log::error("SiteSettingController::updateOrCreatePaymentSetting - Kesalahan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get setting type based on key
     *
     * @param string $key
     * @return string
     */
    private function getSettingType($key)
    {
        if (strpos($key, '_image') !== false || strpos($key, '_logo') !== false) {
            return 'file';
        } else if (strpos($key, '_enabled') !== false || strpos($key, '_visible') !== false) {
            return 'boolean';
        } else if (strpos($key, '_percentage') !== false || strpos($key, '_price') !== false || strpos($key, '_amount') !== false) {
            return 'numeric';
        } else if (strpos($key, '_description') !== false || strpos($key, '_text') !== false) {
            return 'textarea';
        } else if (strpos($key, '_color') !== false) {
            return 'color';
        } else {
            return 'text';
        }
    }

    /**
     * Show the about page with application information.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        $appInfo = [
            'name' => 'Kedai Coffee kiosk Ordering',
            'version' => '1.3.2',
            'description' => 'Sistem pemesanan mandiri untuk kedai kopi',
            'developer' => 'PT. ALCATRAZZ CORPORATION',
            'publisher' => 'Gibran Ade Bintang',
            'copyright' => '© ' . date('Y') . ' PT. ALCATRAZZ CORPORATION. Hak Cipta Dilindungi Undang-Undang.',
            'release_date' => '2025-06-02',
            'last_update' => '2025-06-14',
            'framework' => 'Laravel ' . app()->version(),
            'php_version' => phpversion(),
        ];

        // Panduan penggunaan aplikasi
        $userGuides = [
            [
                'title' => 'Panduan Admin',
                'sections' => [
                    [
                        'title' => 'Dashboard',
                        'content' => 'Dashboard menampilkan ringkasan data penting seperti total pesanan, pendapatan, produk terlaris, dan aktivitas terbaru. Data diperbarui secara real-time setiap 30 detik.'
                    ],
                    [
                        'title' => 'Mengelola Produk',
                        'content' => 'Anda dapat menambah, mengedit, dan menghapus produk melalui menu Produk. Setiap produk harus memiliki nama, harga, dan kategori. Anda juga dapat mengunggah gambar untuk setiap produk.'
                    ],
                    [
                        'title' => 'Mengelola Kategori',
                        'content' => 'Kategori digunakan untuk mengelompokkan produk. Anda dapat membuat kategori baru dan mengaturnya melalui menu Kategori.'
                    ],
                    [
                        'title' => 'Mengelola Pesanan',
                        'content' => 'Menu Pesanan menampilkan semua pesanan yang masuk. Anda dapat mengubah status pesanan dan melihat detail setiap pesanan.'
                    ],
                    [
                        'title' => 'Pengaturan',
                        'content' => 'Menu Pengaturan memungkinkan Anda mengkonfigurasi informasi toko, tampilan, footer, dan metode pembayaran.'
                    ]
                ]
            ],
            [
                'title' => 'Panduan Kiosk (Frontend)',
                'sections' => [
                    [
                        'title' => 'Halaman Utama',
                        'content' => 'Pelanggan akan melihat halaman pemilihan tipe pesanan (dine-in atau take away) saat pertama kali mengakses kiosk.'
                    ],
                    [
                        'title' => 'Memilih Produk',
                        'content' => 'Pelanggan dapat menjelajahi produk berdasarkan kategori, memilih produk, dan menambahkannya ke keranjang.'
                    ],
                    [
                        'title' => 'Keranjang Belanja',
                        'content' => 'Keranjang menampilkan semua item yang dipilih. Pelanggan dapat mengubah jumlah item atau menghapusnya dari keranjang.'
                    ],
                    [
                        'title' => 'Checkout',
                        'content' => 'Pada halaman checkout, pelanggan dapat memasukkan nama (opsional) dan memilih metode pembayaran yang tersedia.'
                    ],
                    [
                        'title' => 'Konfirmasi Pesanan',
                        'content' => 'Setelah checkout, pelanggan akan melihat halaman konfirmasi dengan nomor pesanan dan instruksi pembayaran.'
                    ]
                ]
            ]
        ];

        return view('admin.settings.about', compact('appInfo', 'userGuides'));
    }
}
