<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v108;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;

class SubscriptionTable extends Migration
{
    public function up()
    {
        if (!$this->schema->hasTable('subscription')) {
            $this->schema->create('subscription', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('stripe_cus_id')->nullable();
                $table->string('status')->nullable();
                $table->string('term')->nullable();

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        $this->schema->drop('subscription');
    }
}