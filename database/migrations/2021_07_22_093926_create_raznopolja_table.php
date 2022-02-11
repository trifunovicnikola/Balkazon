<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaznopoljaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raznopolja', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('razno_vrsta')->unsigned();
            $table->foreign('razno_vrsta')->references('id')->on('razno');
            $table->string('naziv');
            $table->float('cijena');
            $table->string('kontakt');
            $table->string('index');
            $table->string('opis')->nullable();
            $table->string('lokacija')->nullable();
            $table->boolean('placen')->nullable();
            $table->string('stanje')->nullable();
            $table->string('sirina')->nullable();
            $table->string('duzina')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->string('javno')->nullable();
            $table->string('modcijena')->nullable();
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
        Schema::dropIfExists('raznopolja');
    }
}
