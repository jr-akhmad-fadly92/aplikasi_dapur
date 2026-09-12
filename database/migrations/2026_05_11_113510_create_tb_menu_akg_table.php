<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbMenuAkgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_menu_akg', function (Blueprint $table) {
            $table->id();

            $table->integer('id_menu');
            $table->integer('id_nutrisi');

            $table->enum('komponen_sehat', [
                'karbohidrat',
                'protein',
                'sayur',
                'buah',
                'pendamping'
            ]);

            $table->decimal('energi', 10, 2)->default(0);
            $table->decimal('protein', 10, 2)->default(0);
            $table->decimal('lemak', 10, 2)->default(0);
            $table->decimal('karbo', 10, 2)->default(0);
            $table->decimal('serat', 10, 2)->default(0);
            $table->decimal('natrium', 10, 2)->default(0);

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
        Schema::dropIfExists('tb_menu_akg');
    }
}
