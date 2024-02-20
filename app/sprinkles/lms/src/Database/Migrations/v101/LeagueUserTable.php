<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v101;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;


class LeagueUserTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\LeagueTable'
    ];
    public function up()
    {
        if (!$this->schema->hasTable('league_user')) {
            $this->schema->create('league_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('league_id')->unsigned();

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('league_id')->references('id')->on('league');

            });
        }
    }

    public function down()
    {
        $this->schema->drop('league_user');
    }
}