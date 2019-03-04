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
            $table->string('title');                                // 名称
            $table->integer('project_id')->nullable()->unsigned();  // 项目ID
            $table->integer('parent_id')->nullable()->unsigned();   // 父级ID
            $table->decimal('amount', 10, 2);                       // 项目金额
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