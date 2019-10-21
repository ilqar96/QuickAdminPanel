<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->unique();

            $table->longText('content');

            $table->datetime('publish_date');

            $table->string('active');

            $table->timestamps();

            $table->softDeletes();
        });
    }
}
