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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // order, product, category, dll
            $table->string('description');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model_type')->nullable(); // App\Models\Order, App\Models\Product, dll
            $table->unsignedBigInteger('model_id')->nullable();
            $table->timestamps();

            // Foreign key jika perlu
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
