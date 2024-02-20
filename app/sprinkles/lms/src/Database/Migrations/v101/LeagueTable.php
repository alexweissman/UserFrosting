<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v101;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;

class LeagueTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\GameweekTable',
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable'
    ];
    public function up()
    {
        if (!$this->schema->hasTable('league')) {
            $this->schema->create('league', function (Blueprint $table) {
                $table->increments('id');
                $table->string('league_name');
                $table->string('status')->default('pending');
                $table->integer('admin_user_id')->unsigned();
                $table->integer('original_league_id')->nullable();
                $table->string('join_code');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('admin_user_id')->references('id')->on('users');
                $table->foreign('start_gameweek')->references('id')->on('gameweek');

            });
        }
    }

    public function down()
    {
        $this->schema->drop('league');
    }
}