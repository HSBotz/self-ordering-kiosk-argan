<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('text'); // text, textarea, image, boolean, etc
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default footer settings
        $this->insertDefaultSettings();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_settings');
    }

    /**
     * Insert default settings
     *
     * @return void
     */
    private function insertDefaultSettings()
    {
        $settings = [
            // Footer settings
            [
                'key' => 'footer_about_title',
                'value' => 'Kedai Coffee Kiosk',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Judul kolom tentang di footer'
            ],
            [
                'key' => 'footer_about_text',
                'value' => 'Nikmati kopi premium kami dengan layanan self-ordering yang mudah dan cepat.',
                'group' => 'footer',
                'type' => 'textarea',
                'description' => 'Teks kolom tentang di footer'
            ],
            [
                'key' => 'footer_social_facebook',
                'value' => '#',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'URL Facebook'
            ],
            [
                'key' => 'footer_social_instagram',
                'value' => '#',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'URL Instagram'
            ],
            [
                'key' => 'footer_social_twitter',
                'value' => '#',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'URL Twitter'
            ],
            [
                'key' => 'footer_hours_title',
                'value' => 'Jam Buka',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Judul kolom jam buka di footer'
            ],
            [
                'key' => 'footer_hours_weekday',
                'value' => 'Senin - Jumat: 08:00 - 22:00',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Jam buka hari kerja'
            ],
            [
                'key' => 'footer_hours_weekend',
                'value' => 'Sabtu - Minggu: 09:00 - 23:00',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Jam buka akhir pekan'
            ],
            [
                'key' => 'footer_contact_title',
                'value' => 'Kontak',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Judul kolom kontak di footer'
            ],
            [
                'key' => 'footer_contact_address',
                'value' => 'Jl. Kopi No. 123, Kota',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Alamat di footer'
            ],
            [
                'key' => 'footer_contact_phone',
                'value' => '+62 123 4567 890',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Nomor telepon di footer'
            ],
            [
                'key' => 'footer_contact_email',
                'value' => 'info@kedaicoffee.com',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Email di footer'
            ],
            [
                'key' => 'footer_copyright',
                'value' => 'Kedai Coffee Kiosk. Semua hak dilindungi.',
                'group' => 'footer',
                'type' => 'text',
                'description' => 'Teks copyright di footer'
            ],
        ];

        DB::table('site_settings')->insert($settings);
    }
};
