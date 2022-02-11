<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutomotopoljaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automotopolja', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('automoto_vrsta')->unsigned();
            $table->foreign('automoto_vrsta')->references('id')->on('automoto');
            $table->string('naziv');
            $table->string('marka');
            $table->string('model');
            $table->integer('cijena');
            $table->string('kontakt');
            $table->string('index');
            $table->string('stanje')->nullable();
            $table->integer('godina_proizvodnje')->nullable();
            $table->integer('kilometraza')->nullable();
            $table->integer('kubikaza')->nullable();
            $table->string('boja')->nullable();
            $table->boolean('registrovan')->nullable();
            $table->string('datum_isteka')->nullable();
            $table->string('opis')->nullable();
            $table->string('lokacija')->nullable();
            $table->string('sirina')->nullable();
            $table->string('duzina')->nullable();
            $table->boolean('placen')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->string('javno')->nullable();
            $table->integer('modcijena')->nullable();
            $table->string('procenat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automotopolja');
    }
}
