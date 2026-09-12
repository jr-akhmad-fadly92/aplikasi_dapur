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
        Schema::create('tb_gizi_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paket')->constrained('tb_paket_menu')->onDelete('cascade');
            $table->decimal('energi', 8, 2)->comment('Nilai energi dalam kkal');
            $table->decimal('protein', 8, 2)->comment('Protein dalam gram');
            $table->decimal('lemak', 8, 2)->comment('Lemak dalam gram');
            $table->decimal('karbohidrat', 8, 2)->comment('Karbohidrat dalam gram');
            $table->decimal('serat', 8, 2)->comment('Serat dalam gram');
            $table->decimal('natrium', 8, 2)->comment('Natrium dalam gram');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_gizi_menu');
    }
};
