<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbOmprengTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_ompreng', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_dapur')->nullable()->comment("id dapur jika diberlakukan untuk masing2 dapur");
            $table->string('kode_ompreng')->comment("kode qr untuk ompreng atau rantang");
            $table->enum('jenis', ['Ompreng','Rantang'])->comment("jenis barang ompreng atau rantang");
            $table->enum('status', ['Didapur','Diluar', 'Rusak/Hilang'])->default('Didapur')->comment("status barang");
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_ompreng');
    }
}
