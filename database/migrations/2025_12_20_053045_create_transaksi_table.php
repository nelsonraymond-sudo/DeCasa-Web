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
    Schema::create('transaksi', function (Blueprint $table) {
        $table->string('id_trans', 10)->primary();
        $table->string('id_user', 5);
        $table->string('id_properti', 5);
        $table->string('id_metode', 5);
        $table->dateTime('tgl_trans');
        $table->date('checkin');
        $table->date('checkout');
        $table->integer('durasi');
        $table->decimal('total_harga', 12, 2);
        $table->enum('status', ['pending', 'lunas', 'selesai', 'batal']);
        $table->timestamps();

        $table->foreign('id_user')->references('id_user')->on('users');
        $table->foreign('id_properti')->references('id_properti')->on('properti');
        $table->foreign('id_metode')->references('id_metode')->on('payment');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
