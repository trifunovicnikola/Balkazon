<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlikaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slika', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('slika_tehnika')->unsigned()->nullable();
            $table->foreign('slika_tehnika')->references('id')->on('tehnikapolja');
            $table->bigInteger('slika_hrana')->unsigned()->nullable();
            $table->foreign('slika_hrana')->references('id')->on('hranapolja');
            $table->bigInteger('slika_nekretnine')->unsigned()->nullable();
            $table->foreign('slika_nekretnine')->references('id')->on('nekretninepolja');
            $table->bigInteger('slika_automoto')->unsigned()->nullable();
            $table->foreign('slika_automoto')->references('id')->on('automotopolja');
            $table->bigInteger('slika_odjeca')->unsigned()->nullable();
            $table->foreign('slika_odjeca')->references('id')->on('odjecapolja');
            $table->bigInteger('slika_posao')->unsigned()->nullable();
            $table->foreign('slika_posao')->references('id')->on('posaopolja');
            $table->bigInteger('slika_razno')->unsigned()->nullable();
            $table->foreign('slika_razno')->references('id')->on('raznopolja');

            $table->string('url');
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
        Schema::dropIfExists('slika');
    }
}
