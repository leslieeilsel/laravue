<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_ledger', function (Blueprint $table) {
            $table->increments('id');
            $table->string('year')->nullable();     //年度   
            $table->string('quarter')->nullable();     //填报月份   
            $table->string('project_id')->nullable();     //项目名称->项目表id
            $table->string('project_num')->nullable();     //项目编号
            $table->string('nature')->nullable();     //建设性质 
            $table->string('subject')->nullable();     //投资主体   
            $table->decimal('total_investors',10,2)->nullable();     //总投资  
            $table->string('scale_con')->nullable();     //主要建设规模及主要内容
            $table->decimal('plan_investors',10,2)->nullable();     //2019年计划投资 
            $table->string('plan_con')->nullable();     //2019年主要建设内容
            $table->string('quarter_progress')->nullable();     //季度项目进度
            $table->string('problem')->nullable();     //存在问题
            $table->integer('user_id')->nullable();     //用户id
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
        Schema::dropIfExists('iba_project_ledger');
    }
}
