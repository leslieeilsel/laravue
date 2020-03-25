<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_operation_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('操作名称');
            $table->string('method')->comment('请求类型');
            $table->string('path')->comment('请求路径');
            $table->string('ip')->comment('ip地址');
            $table->string('ip_place')->comment('ip信息');
            $table->integer('user_id')->comment('操作用户');
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
        Schema::dropIfExists('sys_operation_logs');
    }
}
