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
            $table->text('num')->nullable();                      // 项目编号
            $table->integer('type');                                // 项目类型
            $table->integer('status');                              // 项目状态
            $table->string('subject');                              // 投资主体
            $table->string('unit');                                 // 承建单位
            $table->integer('build_type');                          // 建设性质
            $table->decimal('amount', 10, 2);                       // 项目金额（总金额）
            $table->integer('money_from');                          // 资金来源
            $table->decimal('land_amount', 10, 2)->nullable();      // 土地费用
            $table->integer('is_gc');                               // 改创项目
            $table->integer('nep_type')->nullable();                // 国民经济计划分类(nep:national economic plan)
            $table->integer('is_audit');                            // 审核状态
            $table->integer('audited');                             // 是否经过审核（审核过的项目不能再删除）
            $table->string('reason')->nullable();                   // 审核不通过原因
            $table->string('description')->nullable();              // 投资概况
            $table->json('center_point')->nullable();               // 项目中心点坐标
            $table->json('positions')->nullable();                  // 项目坐标集
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
