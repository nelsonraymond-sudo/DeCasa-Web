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
    Schema::create('foto', function (Blueprint $table) {
        $table->integer('id_foto')->autoIncrement(); // Integer Auto Increment
        $table->string('id_properti', 5);
        $table->string('url_foto');
        $table->timestamps();

        $table->foreign('id_properti')->references('id_properti')->on('properti')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto');
    }
};
