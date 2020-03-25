<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysDictDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_dict_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('名称');
            $table->string('value')->comment('字段值');
            $table->unsignedInteger('dict_id')->comment('字典ID');
            $table->string('description')->nullable()->comment('描述');
            $table->integer('created_user_id')->nullable()->comment('创建人');
            $table->integer('updated_user_id')->nullable()->comment('修改人');
            $table->integer('sort')->default(null)->comment('排序值');
            $table->integer('status')->default(1)->comment('是否启用');
            $table->timestamps();
            // 添加外键
            $table->foreign('dict_id')->references('id')->on('sys_dict_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_dict_data');
    }
}
