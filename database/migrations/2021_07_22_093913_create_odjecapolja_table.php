<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdjecapoljaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odjecapolja', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('odjeca_vrsta')->unsigned();
            $table->foreign('odjeca_vrsta')->references('id')->on('odjeca');
            $table->string('naziv');
            $table->float('cijena');
            $table->string('kontakt');
            $table->string('index');
            $table->string('opis')->nullable();
            $table->string('stanje')->nullable();
            $table->string('lokacija')->nullable();
            $table->string('sirina')->nullable();
            $table->string('duzina')->nullable();
            $table->boolean('placen')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('dimenzije')->nullable();
            $table->string('javno')->nullable();
            $table->string('modcijena')->nullable();
            $table->string('procenat')->nullable();
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
        Schema::dropIfExists('odjecapolja');
    }
}
