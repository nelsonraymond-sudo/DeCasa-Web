<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detailfasilitas', function (Blueprint $table) {
            // UBAH DARI: $table->string('id_detail', 5)->primary();
            // MENJADI:
            $table->id('id_detail'); // Ini otomatis membuat kolom 'id' (Big Integer, Auto Increment, Primary)

            $table->string('id_properti', 5);
            $table->string('id_fasilitas', 5);
            $table->timestamps();

            $table->foreign('id_properti')->references('id_properti')->on('properti')->onDelete('cascade');
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detailfasilitas');
    }
};