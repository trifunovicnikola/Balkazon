<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNekretninepoljaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nekretninepolja', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nekretnine_vrsta')->unsigned();
            $table->foreign('nekretnine_vrsta')->references('id')->on('nekretnine');
            $table->string('naziv');
            $table->float('kvadratura');
            $table->integer('cijena');
            $table->string('kontakt');
            $table->string('tip_vlasnistva');
            $table->string('index');
            $table->string('opis')->nullable();
            $table->string('lokacija')->nullable();
            $table->string('sirina')->nullable();
            $table->string('duzina')->nullable();
            $table->boolean('placen')->nullable();
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
        Schema::dropIfExists('nekretninepolja');
    }
}
