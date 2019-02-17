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
            $table->string('title');                                    // 项目名称
            $table->string('warning_title');                            // 预警名称
            $table->integer('project_info_id')->nullable()->unsigned(); // 项目信息ID
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
