<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v106;

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
        $this->schema->table('round_user', function (Blueprint $table) {
            $table->boolean('paid_entry_fee')->nullable()->default(false);
        });
    }

    public function down()
    {
        $this->schema->table('round_user', function (Blueprint $table) {
            $table->dropTable('paid_entry_fee');
        });
    }
}