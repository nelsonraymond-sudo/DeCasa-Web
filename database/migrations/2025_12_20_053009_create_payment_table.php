<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('payment', function (Blueprint $table) {
        $table->string('id_metode', 5)->primary();
        $table->string('nama_bank', 50);
        $table->string('no_rekening', 50);
        $table->string('atas_nama', 100);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
