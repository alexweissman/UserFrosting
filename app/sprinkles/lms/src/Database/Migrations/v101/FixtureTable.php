<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v101;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;
use UserFrosting\Sprinkle\Core\Facades\Seeder;


class FixtureTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\GameweekTable',
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\TeamTable',
    ];
    public function up()
    {
        if (!$this->schema->hasTable('fixture')) {
            $this->schema->create('fixture', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('home_team')->unsigned();
                $table->integer('away_team')->unsigned();
                $table->integer('gameweek_id')->unsigned();
                $table->integer('result')->nullable()->unsigned()->default(null);
                $table->integer('home_difficulty');
                $table->integer('away_difficulty');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('home_team')->references('id')->on('team');
                $table->foreign('away_team')->references('id')->on('team');
                $table->foreign('gameweek_id')->references('id')->on('gameweek');
                $table->foreign('result')->references('id')->on('team');

            });
            // Add default groups
            //Seeder::execute('DefaultFixtures');
        }
    }

    public function down()
    {
        $this->schema->drop('fixture');
    }
}