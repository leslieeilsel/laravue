<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                                // 项目名称
            $table->string('num');                                  // 项目编号
            $table->string('status');                               // 建设状态
            $table->string('owner');                                // 业主
            $table->string('subject');                              // 投资主体
            $table->string('unit');                                 // 建设单位
            $table->decimal('amount', 10, 2);                       // 项目金额
            $table->decimal('safe_amount', 10, 2);                  // 建安投资
            $table->decimal('land_amount', 10, 2);                  // 土地费用
            $table->string('type');                                 // 项目类型
            $table->string('is_gc');                                // 项目标识
            $table->string('is_edit');                              // 编辑状态
            $table->string('description');                          // 投资概况
            $table->string('center_point');                         // 项目中心点坐标
            $table->string('positions');                            // 项目坐标集
            $table->date('plan_start_at')->nullable();              // 计划开始时间
            $table->date('plan_end_at')->nullable();                // 计划结束时间
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
        Schema::dropIfExists('iba_project_projects');
    }
}
