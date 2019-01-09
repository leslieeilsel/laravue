<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaSystemDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_system_department', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                                // 名称
            $table->integer('sort')->default(null);                 // 排序值
            $table->integer('status')->default(1);                  // 是否启用
            $table->integer('parent_id')->nullable()->unsigned();   // 父级ID
            $table->text('description')->nullable();                // 描述
            $table->string('create_by')->nullable();                // 创建人
            $table->string('update_by')->nullable();                // 修改人
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
        Schema::dropIfExists('iba_system_department');
    }
}
