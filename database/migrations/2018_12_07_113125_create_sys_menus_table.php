<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题');
            $table->string('name')->comment('英文标题');
            $table->string('component')->nullable()->comment('前端组件');
            $table->integer('parent_id')->nullable()->unsigned()->comment('父级ID');
            $table->string('description')->nullable()->comment('描述');
            $table->string('path')->comment('地址');
            $table->integer('link_type')->default(0)->comment('链接类型');
            $table->string('url')->nullable()->comment('第三方链接');
            $table->string('icon', 50)->nullable()->comment('icon');
            $table->string('target')->default('_self')->comment('链接跳转类型');
            $table->integer('enabled')->default(1)->comment('是否启用');
            $table->integer('sort')->default(0)->comment('排序值');
            $table->integer('created_user_id')->nullable()->comment('创建用户ID');
            $table->integer('updated_user_id')->nullable()->comment('修改用户ID');
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
        Schema::dropIfExists('sys_menus');
    }
}
