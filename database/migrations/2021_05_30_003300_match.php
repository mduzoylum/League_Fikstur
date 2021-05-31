<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Match extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("round");
            $table->unsignedInteger("home_team");
            $table->integer("home_team_score")->nullable();
            $table->integer("home_team_performance")->nullable();
            $table->smallInteger("home_team_is_win")->nullable();
            $table->unsignedInteger("away_team");
            $table->integer("away_team_score")->nullable();
            $table->integer("away_team_performance")->nullable();
            $table->smallInteger("away_team_is_win")->nullable();
            $table->boolean("is_draw")->default(false);

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
        Schema::dropIfExists('match');
    }
}
