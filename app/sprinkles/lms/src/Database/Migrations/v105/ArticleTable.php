<?php

namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v105;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;


class ArticleTable extends Migration
{
    public function up()
    {
        if (!$this->schema->hasTable('article')) {
            $this->schema->create('article', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('subtitle');
                $table->string('article');
                $table->string('meta_description');
                $table->string('meta_keywords');
                $table->string('slug');

                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
            });
        }
    }

    public function down()
    {
        $this->schema->drop('article');
    }
}