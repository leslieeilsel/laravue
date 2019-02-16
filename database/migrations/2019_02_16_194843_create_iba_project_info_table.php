<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaProjectInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_project_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                                // 项目名称
            $table->string('owner')->nullable();                    // 业主
            $table->string('unit')->nullable();                     // 建设单位
            $table->decimal('amount', 8, 2)->nullable();            // 项目金额
            $table->integer('parent_id')->nullable()->unsigned();   // 父级ID
            $table->date('plan_start_at')->nullable();              // 计划开始时间
            $table->date('plan_end_at')->nullable();                // 计划结束时间
            $table->date('actual_start_at')->nullable();            // 实际开始时间
            $table->date('actual_end_at')->nullable();              // 实际结束时间
            $table->string('longitude', 50)->nullable();            // 经度
            $table->string('latitude', 50)->nullable();             // 纬度
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
        Schema::dropIfExists('iba_project_info');
    }
}
