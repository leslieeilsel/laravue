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
            $table->string('en_title');                             // 英文标题
            $table->string('frontend_component');                   // 前端组件
            $table->integer('parent_id')->nullable()->unsigned();   // 父级ID
            $table->string('description')->nullable();              // 描述
            $table->string('url')->nullable();                      // 地址
            $table->string('vector_icon', 50)->default('');         // icon
            $table->string('link_target')->default('_self');        // 链接调转类型
            $table->integer('enabled')->default(1);                 // 是否启用
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
