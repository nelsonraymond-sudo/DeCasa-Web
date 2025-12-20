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
    Schema::create('detailfasilitas', function (Blueprint $table) {
        $table->string('id_detail', 5)->primary();
        $table->string('id_properti', 5);
        $table->string('id_fasilitas', 5);
        $table->timestamps();

        $table->foreign('id_properti')->references('id_properti')->on('properti')->onDelete('cascade');
        $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailfasilitas');
    }
};
