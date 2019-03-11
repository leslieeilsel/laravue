<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('month')->nullable();                        // 填报月份 
            $table->string('project_id')->nullable();                   // 项目名称->项目表id
            $table->string('project_num')->nullable();                  // 项目编号
            $table->string('subject')->nullable();                      // 投资主体
            $table->string('build_start_at')->nullable();               // 建设开始年限
            $table->string('build_end_at')->nullable();                 // 建设结束年限
            $table->decimal('total_investors', 14, 4)->nullable();      // 总投资
            $table->decimal('plan_investors', 14, 4)->nullable();       // 2019年计划投资
            $table->string('plan_img_progress')->nullable();            // 2019年计划形象进度
            $table->string('start_month_img_progress')->nullable();     // 1- 月形象进度
            $table->string('start_act_complete')->nullable();           // 1- 月实际完成投资
            $table->string('month_img_progress')->nullable();           // x月形象进度
            $table->string('month_act_complete')->nullable();           // x月实际完成投资
            $table->string('acc_complete')->nullable();                 // 自开始累积完成投资
            $table->string('problem')->nullable();                      // 存在问题
            $table->string('start_at')->nullable();                     // 开工时间
            $table->string('plan_start_at')->nullable();                // 计划开工时间
            $table->string('exp_preforma')->nullable();                 // 土地征收情况及前期手续办理情况
            $table->string('img_progress_pic')->nullable();             // 形象进度照片
            $table->string('marker')->nullable();                       // 备注
            $table->integer('is_audit')->default(0);                    // 审核状态
            $table->integer('user_id')->nullable();                     // 用户id
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
        Schema::dropIfExists('iba_project_schedule');
    }
}
