<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v102;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;
use UserFrosting\Sprinkle\Core\Facades\Seeder;


class PickTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v103\RoundTable',
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\LeagueTable'

    ];
    public function up()
    {
        if (!$this->schema->hasTable('pick')) {
            $this->schema->create('pick', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('gameweek_id')->unsigned();
                $table->integer('round_user_id')->unsigned();
                $table->integer('team_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->string('result');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('gameweek_id')->references('id')->on('gameweek');
                $table->foreign('round_id')->references('id')->on('round_user');
                $table->foreign('team_id')->references('id')->on('team');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        $this->schema->drop('pick');
    }
}