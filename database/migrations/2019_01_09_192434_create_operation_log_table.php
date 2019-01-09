<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');    // 操作名称
            $table->string('method');   // 请求类型
            $table->string('path');     // 请求路径
            $table->string('ip');       // ip地址
            $table->string('ip_place'); // ip信息
            $table->integer('user_id'); // 操作用户
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_log');
    }
}
