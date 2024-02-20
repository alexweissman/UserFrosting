<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v103;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;

class RoundTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\GameweekTable',
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\LeagueTable'

    ];
    public function up()
    {
        if (!$this->schema->hasTable('round')) {
            $this->schema->create('round', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('start_gameweek')->unsigned();
                $table->integer('current_gameweek')->unsigned();
                $table->integer('league_id')->unsigned();
                $table->string('status')->default('pending');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('start_gameweek')->references('id')->on('gameweek');
                $table->foreign('current_gameweek')->references('id')->on('gameweek');
                $table->foreign('league_id')->references('id')->on('league');

            });
        }
    }

    public function down()
    {
        $this->schema->drop('round');
    }
}