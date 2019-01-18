<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaSystemDictDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_system_dict_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                            // 名称
            $table->string('value');                            // 字段值
            $table->unsignedInteger('dict_id');                 // 字典ID
            $table->string('description')->nullable();          // 描述
            $table->integer('created_user_id')->nullable();     // 创建人
            $table->integer('updated_user_id')->nullable();     // 修改人
            $table->integer('sort')->default(null);             // 排序值
            $table->integer('status')->default(1);              // 是否启用
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
        Schema::dropIfExists('iba_system_dict_data');
    }
}
