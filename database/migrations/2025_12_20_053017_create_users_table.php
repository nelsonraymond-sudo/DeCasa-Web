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
    Schema::create('users', function (Blueprint $table) {
        $table->string('id_user', 5)->primary();
        $table->string('nm_user', 100);
        $table->string('email', 100)->unique();
        $table->string('pass'); 
        $table->enum('role', ['admin', 'customer']);
        $table->string('no_hp', 20);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
