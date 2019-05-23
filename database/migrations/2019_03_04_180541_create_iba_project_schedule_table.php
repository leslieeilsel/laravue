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
            $table->integer('project_id');                              // 项目名称->项目表id
            $table->string('month')->nullable();                        // 填报月份
            $table->decimal('month_act_complete', 10, 2)->nullable();   // x月实际完成投资
            $table->string('month_img_progress')->nullable();           // x月形象进度
            $table->string('acc_img_progress')->nullable();             // x月形象进度
            $table->text('problem')->nullable();                        // 存在问题
            $table->text('measures')->nullable();                       // 整改措施
            $table->text('exp_preforma')->nullable();                   // 土地征收情况及前期手续办理情况
            $table->text('img_progress_pic')->nullable();               // 形象进度照片
            $table->string('marker')->nullable();                       // 备注
            $table->integer('is_audit')->default(0);                    // 审核状态
            $table->string('reason')->nullable();                       // 审核不通过原因
            $table->integer('plan_id')->nullable();                     // 计划id
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
