<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMasterBahan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //tabel master bumbu dan bahan masakan
        Schema::create('tb_master_bahan', function (Blueprint $table) {
            $table->id();
            $table->string('bahan', 30); // Kolom bahan
            $table->float('gramasi', 10, 2)->nullable(); // Kolom gramasi dengan format float
            $table->unsignedBigInteger('satuan_gudang')->nullable(); // Foreign key ke tb_satuan
            $table->unsignedBigInteger('satuan_bahan'); // Foreign key ke tb_satuan
            $table->integer('jenis')->nullable(); // Foreign key ke Jenis
            $table->timestamps(); // Set foreign key untuk 'satuan_gudang' dan 'satuan_bahan'
            
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_master_bahan');
    }
}
