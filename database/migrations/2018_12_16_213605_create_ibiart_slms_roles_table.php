<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbiartSlmsRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ibiart_slms_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();         // 角色名称
            $table->string('description')->nullable();  // 描述
            $table->integer('is_default')->default(0);  // 是否设置为注册用户默认角色
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
        Schema::dropIfExists('ibiart_slms_roles');
    }
}
