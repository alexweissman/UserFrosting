<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v107;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;

use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Lms\Database\Models\MarketingConsent;

class MarketingConsentTable extends Migration
{
    public function up()
    {
        if (!$this->schema->hasTable('marketing_consent')) {
            $this->schema->create('marketing_consent', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->boolean('consented')->default(0);
                $table->string('klaviyo_id')->nullable();

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->foreign('user_id')->references('id')->on('users');
            });
        }

        $users = User::get();
        foreach ($users as $user){
            $consent = new MarketingConsent([
                'user_id' => $user->id,
                'consented' => 0
            ]);
            $consent->save();
        }
    }

    public function down()
    {
        $this->schema->drop('marketing_consent');
    }
}