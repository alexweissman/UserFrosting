<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v103;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;


class RoundUserTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\LeagueTable',
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v103\RoundTable',
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable'
    ];
    public function up()
    {
        if (!$this->schema->hasTable('round_user')) {
            $this->schema->create('round_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('round_id')->unsigned();
                $table->string('user_status')->default('active');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('round_id')->references('id')->on('round');
            });
        }
    }

    public function down()
    {
        $this->schema->drop('round_user');
    }
}