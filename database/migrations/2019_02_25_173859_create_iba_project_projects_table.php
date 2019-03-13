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
            $table->integer('type');                                // 项目类型
            $table->integer('status');                              // 项目状态
            $table->string('subject');                              // 投资主体
            $table->string('unit');                                 // 承建单位
            $table->integer('build_type');                          // 建设性质
            $table->decimal('amount', 14, 4);                       // 项目金额（总金额）
            $table->integer('money_from');                          // 资金来源
            $table->decimal('land_amount', 14, 4)->nullable();      // 土地费用
            $table->integer('is_gc');                               // 改创项目
            $table->integer('nep_type')->nullable();                // 国民经济计划分类(nec:national economic plan)
            $table->integer('is_audit');                            // 审核状态
            $table->integer('is_edit');                             // 编辑状态
            $table->string('description');                          // 投资概况
            $table->string('center_point');                         // 项目中心点坐标
            $table->string('positions');                            // 项目坐标集
            $table->string('plan_start_at');                        // 计划开始年月
            $table->string('plan_end_at');                          // 计划结束年月
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
        Schema::dropIfExists('iba_project_projects');
    }
}
