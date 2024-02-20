<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v101;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class GameweekTable extends Migration
{
    public function up()
    {
        if (!$this->schema->hasTable('gameweek')) {
            $this->schema->create('gameweek', function (Blueprint $table) {
                $table->increments('id');
                $table->string('status')->default('pending');
                $table->tinyInteger('gameweek_number');
                $table->tinyInteger('gameweek_year')->nullable();
                $table->dateTime('deadline');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

            });
            
            // Add default groups
            Seeder::execute('DefaultGameweeks');
        }
    }

    public function down()
    {
        $this->schema->drop('gameweek');
    }
}