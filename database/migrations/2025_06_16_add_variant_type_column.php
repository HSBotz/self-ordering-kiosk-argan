<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cek apakah kolom sudah ada
        if (!Schema::hasColumn('order_items', 'variant_type')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('variant_type')->nullable()->after('notes')
                      ->comment('hot, ice, atau null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_items', 'variant_type')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('variant_type');
            });
        }
    }
};
