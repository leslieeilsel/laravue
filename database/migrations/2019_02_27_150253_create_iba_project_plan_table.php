<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('date');                                // 时间划分（包括年和月）
            $table->integer('project_id')->unsigned();              // 项目ID
            $table->integer('parent_id')->unsigned();               // 父级ID
            $table->decimal('amount', 10, 2);                       // 项目金额
            $table->text('image_progress')->nullable();             // 形象进度
            $table->integer('user_id')->nullable();                 // 用户id
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
        Schema::dropIfExists('iba_project_plan');
    }
}
