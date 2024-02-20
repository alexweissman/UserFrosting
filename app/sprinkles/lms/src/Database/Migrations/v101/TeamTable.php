<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v101;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;
use UserFrosting\Sprinkle\Core\Facades\Seeder;


class TeamTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Lms\Database\Migrations\v101\GameweekTable'
    ];
    public function up()
    {
        if (!$this->schema->hasTable('team')) {
            $this->schema->create('team', function (Blueprint $table) {
                $table->increments('id');
                $table->string('team_name');
                $table->string('team_name_shortened');
                $table->string('image_name');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
            });

            // Add default groups
            Seeder::execute('DefaultTeams');
        }
        
    }

    public function down()
    {
        $this->schema->drop('team');
    }
}