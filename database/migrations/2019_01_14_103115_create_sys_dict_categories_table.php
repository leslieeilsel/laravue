<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysDictCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_dict_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('名称');
            $table->string('type')->comment('唯一名称');
            $table->string('description')->nullable()->comment('描述');
            $table->integer('created_user_id')->nullable()->comment('创建人');
            $table->integer('updated_user_id')->nullable()->comment('修改人');
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
        Schema::dropIfExists('sys_dict_categories');
    }
}
