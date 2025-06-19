<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Waktu sekarang untuk created_at dan updated_at
        $now = now();

        // Log sebelum menjalankan seeder
        Log::info('Menjalankan PaymentSettingsSeeder...');

        $settings = [
            [
                'key' => 'payment_tax_percentage',
                'value' => '10',
                'group' => 'payment',
                'type' => 'number',
                'description' => 'Persentase pajak yang dikenakan pada pembelian'
            ],
            [
                'key' => 'payment_currency',
                'value' => 'Rp',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Simbol mata uang yang digunakan'
            ],
            [
                'key' => 'payment_decimal_separator',
                'value' => '.',
                'group' => 'payment',
                'type' => 'select',
                'description' => 'Karakter pemisah desimal'
            ],
            [
                'key' => 'payment_thousand_separator',
                'value' => '.',
                'group' => 'payment',
                'type' => 'select',
                'description' => 'Karakter pemisah ribuan'
            ],
            [
                'key' => 'payment_currency_position',
                'value' => 'before',
                'group' => 'payment',
                'type' => 'select',
                'description' => 'Posisi simbol mata uang (sebelum atau sesudah angka)'
            ],
            [
                'key' => 'payment_cash_enabled',
                'value' => '1',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Apakah pembayaran tunai diaktifkan'
            ],
            [
                'key' => 'payment_qris_enabled',
                'value' => '0',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Apakah pembayaran QRIS diaktifkan'
            ],
            [
                'key' => 'payment_qris_image',
                'value' => '',
                'group' => 'payment',
                'type' => 'image',
                'description' => 'Gambar kode QRIS untuk pembayaran'
            ],
            [
                'key' => 'payment_debit_enabled',
                'value' => '0',
                'group' => 'payment',
                'type' => 'boolean',
                'description' => 'Apakah pembayaran kartu debit/kredit diaktifkan'
            ],
            [
                'key' => 'payment_debit_cards',
                'value' => 'Visa, Mastercard, JCB, American Express',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Jenis kartu debit/kredit yang diterima'
            ],
        ];

        // Loop melalui setiap pengaturan dan tambahkan jika belum ada
        foreach ($settings as $setting) {
            $existingSetting = SiteSetting::where('key', $setting['key'])->first();
            if (!$existingSetting) {
                DB::table('site_settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $this->command->info("Ditambahkan pengaturan: {$setting['key']}");
            } else {
                $this->command->info("Pengaturan {$setting['key']} sudah ada, dilewati.");
            }
        }

        // Log setelah menjalankan seeder
        Log::info('PaymentSettingsSeeder selesai dijalankan!');
    }
}
