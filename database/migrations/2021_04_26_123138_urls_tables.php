<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UrlsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->unique(); // varchar(100)
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
        });

        Schema::create('url_checks', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('url_id')->references('id')->on('urls'); // ссылается на поле id таблицы urls
            $table->foreignId('url_id')->constrained('urls'); // тоже самое но короче
            $table->integer('status_code')->nullable();
            $table->string('h1')->nullable();
            $table->string('keywords')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
