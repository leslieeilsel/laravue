<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                                // 标题
            $table->string('name');                                 // 英文标题
            $table->string('component');                            // 前端组件
            $table->integer('parent_id')->nullable()->unsigned();   // 父级ID
            $table->string('description')->nullable();              // 描述
            $table->string('path')->nullable();                     // 地址
            $table->integer('link_type')->default(0);               // 链接类型
            $table->string('url')->nullable();                      // 第三方链接
            $table->string('icon', 50)->default('');                // icon
            $table->string('target')->default('_self');             // 链接调转类型
            $table->integer('enabled')->default(1);                 // 是否启用
            $table->integer('sort')->default(0);                    // 排序值
            $table->integer('created_user_id');                     // 创建用户ID
            $table->integer('updated_user_id');                     // 修改用户ID
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
        Schema::dropIfExists('menus');
    }
}
