<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v106;

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
        $this->schema->table('round', function (Blueprint $table) {
            $table->integer('entry_fee')->nullable(); 
        });
    }

    public function down()
    {
        $this->schema->table('round', function (Blueprint $table) {                
            $table->dropColumn('entry_fee');
        });
    }
}