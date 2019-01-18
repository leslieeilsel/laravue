<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbaSystemDictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iba_system_dict', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');                            // 名称
            $table->string('type');                             // 类型
            $table->string('description')->nullable();          // 描述
            $table->integer('created_user_id')->nullable();     // 创建人
            $table->integer('updated_user_id')->nullable();     // 修改人
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
        Schema::dropIfExists('iba_system_dict');
    }
}
