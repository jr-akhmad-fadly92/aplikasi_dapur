<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_menu_gizi_harian', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_menu');
            $table->decimal('energi', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('lemak', 8, 2)->default(0);
            $table->decimal('karbohidrat', 8, 2)->default(0);
            $table->decimal('serat', 8, 2)->default(0);
            $table->decimal('natrium', 8, 2)->default(0);
            $table->timestamps();

            // optional: add foreign key if tb_menu exists
            // $table->foreign('id_menu')->references('id')->on('tb_menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_menu_gizi_harian');
    }
};
