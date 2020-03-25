<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('名称');
            $table->integer('sort')->default(null)->comment('排序值');
            $table->integer('status')->default(1)->comment('是否启用');
            $table->integer('parent_id')->nullable()->unsigned()->comment('父级ID');
            $table->text('description')->nullable()->comment('描述');
            $table->string('create_by')->nullable()->comment('创建人');
            $table->string('update_by')->nullable()->comment('修改人');
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
        Schema::dropIfExists('sys_departments');
    }
}
