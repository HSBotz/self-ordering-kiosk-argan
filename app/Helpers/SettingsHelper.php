<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Mengambil nilai pengaturan berdasarkan key
     *
     * @param string $key Key pengaturan
     * @param mixed $default Nilai default jika pengaturan tidak ditemukan
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Coba ambil dari cache terlebih dahulu
        $cacheKey = 'setting_' . $key;

        return Cache::remember($cacheKey, 86400, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Mengambil semua pengaturan dalam satu group
     *
     * @param string $group Nama group pengaturan
     * @return array
     */
    public static function getGroup($group)
    {
        $cacheKey = 'settings_group_' . $group;

        return Cache::remember($cacheKey, 86400, function () use ($group) {
            return SiteSetting::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Update atau buat pengaturan baru
     *
     * @param string $key Key pengaturan
     * @param mixed $value Nilai pengaturan
     * @param string $group Group pengaturan (default: 'general')
     * @return bool
     */
    public static function set($key, $value, $group = 'general')
    {
        $setting = SiteSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group
            ]
        );

        // Hapus cache untuk pengaturan ini
        Cache::forget('setting_' . $key);
        Cache::forget('settings_group_' . $group);

        return $setting ? true : false;
    }
}
