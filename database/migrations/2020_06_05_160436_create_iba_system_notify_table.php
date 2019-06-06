<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaSystemNotifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_system_notify', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('type')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iba_system_notify');
    }
}
