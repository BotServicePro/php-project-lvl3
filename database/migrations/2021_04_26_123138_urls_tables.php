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
                $table->id();
                $table->string('name', 100)->unique();
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
        });

        Schema::create('url_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->constrained('urls');
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
