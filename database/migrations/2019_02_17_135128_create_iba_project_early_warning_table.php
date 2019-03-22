<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectEarlyWarningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_early_warning', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('warning_type');            // 预警类型
            $table->integer('schedule_id')->unsigned(); // 项目信息ID
            $table->string('schedule_at')->nullable();  // 填报时间
            $table->integer('user_id')->nullable();     // 用户id
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
        Schema::dropIfExists('iba_project_early_warning');
    }
}
