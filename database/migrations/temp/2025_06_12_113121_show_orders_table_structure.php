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
        // Dapatkan struktur tabel orders
        $columns = DB::select('SHOW COLUMNS FROM orders');
        echo "Struktur tabel orders:\n";
        foreach ($columns as $column) {
            echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Default . "\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
