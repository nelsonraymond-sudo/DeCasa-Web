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
    Schema::create('properti', function (Blueprint $table) {
        $table->string('id_properti', 5)->primary();
        $table->string('id_user', 5);
        $table->string('id_kategori', 5);
        $table->string('nm_properti', 100);
        $table->text('deskripsi');
        $table->text('alamat');
        $table->decimal('harga', 12, 2);
        $table->enum('status', ['tersedia', 'penuh']);
        $table->timestamps();

        $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properti');
    }
};
