<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Result extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("team_id");
            $table->integer("pts")->default(0);
            $table->integer("play")->default(0);
            $table->integer("win")->default(0);
            $table->integer("draw")->default(0);
            $table->integer("lost")->default(0);
            $table->integer("gd")->default(0);
            $table->integer("gf")->default(0);
            $table->integer("ga")->default(0);

            $table->foreign('team_id')->references("id")->on("teams");

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
        Schema::dropIfExists('result');
    }
}
