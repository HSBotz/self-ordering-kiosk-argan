<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Get all settings as key-value pairs
     *
     * @param string|null $group
     * @return array
     */
    public static function getAllSettings($group = null)
    {
        $query = self::query();

        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }

    /**
     * Update setting value by key
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public static function updateValue($key, $value, $group = null)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info("SiteSetting::updateValue - key: {$key}, value: {$value}, type: " . gettype($value));

        $setting = self::where('key', $key)->first();

        if (!$setting) {
            // Tentukan group berdasarkan prefiks key jika tidak ada
            if ($group === null) {
                if (strpos($key, 'store_') === 0) {
                    $group = 'store';
                } elseif (strpos($key, 'footer_') === 0) {
                    $group = 'footer';
                } elseif (strpos($key, 'appearance_') === 0) {
                    $group = 'appearance';
                } else {
                    $group = 'general';
                }
            }

            // Tentukan tipe berdasarkan key
            $type = 'text';
            if (strpos($key, '_description') !== false) {
                $type = 'textarea';
            } elseif (strpos($key, '_color') !== false) {
                $type = 'color';
            } elseif (strpos($key, '_visible') !== false || strpos($key, '_enabled') !== false) {
                $type = 'boolean';
            }

            // Jika setting tidak ditemukan, buat baru
            $newSetting = self::create([
                'key' => $key,
                'value' => (string) $value,
                'group' => $group,
                'type' => $type,
                'description' => 'Auto generated setting'
            ]);

            \Illuminate\Support\Facades\Log::info("SiteSetting::updateValue - Created new setting: {$key} = {$value}, group: {$group}, success: " . ($newSetting ? 'true' : 'false'));

            // Verifikasi pengaturan baru
            $verifiedSetting = self::where('key', $key)->first();
            \Illuminate\Support\Facades\Log::info("SiteSetting::updateValue - Verifikasi setelah create: {$key} = " .
                ($verifiedSetting ? $verifiedSetting->value : 'not found'));

            return $newSetting ? true : false;
        }

        // Pastikan nilai dikonversi ke string secara eksplisit (penting untuk nilai boolean terutama 0)
        $oldValue = $setting->value;
        $setting->value = (string) $value;
        $saved = $setting->save();

        // Flush cache setelah update
        \Illuminate\Support\Facades\Cache::forget($setting->group . '_settings');

        // Tambahan verifikasi untuk memastikan nilai benar-benar disimpan
        $verifiedSetting = self::where('key', $key)->first();
        \Illuminate\Support\Facades\Log::info("SiteSetting::updateValue - Verifikasi nilai {$key} setelah update: " .
            ($verifiedSetting ? $verifiedSetting->value : 'not found'));

        \Illuminate\Support\Facades\Log::info("SiteSetting::updateValue - Updated setting: {$key} from {$oldValue} to {$setting->value}, success: " . ($saved ? 'true' : 'false'));

        return $saved;
    }
}
