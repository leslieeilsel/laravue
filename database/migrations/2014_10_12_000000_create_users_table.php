<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->comment('用户名');
            $table->string('name')->comment('姓名');
            $table->string('phone')->nullable()->comment('电话');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('password')->comment('密码');
            $table->string('desc')->nullable()->comment('详细信息');
            $table->unsignedInteger('department_id')->nullable()->comment('部门id');
            $table->integer('office')->nullable()->comment('职务');
            $table->unsignedInteger('group_id')->nullable()->comment('角色');
            $table->rememberToken()->nullable()->comment('token');
            $table->string('ding_user_id')->nullable()->comment('钉钉id');
            $table->timestamp('last_login')->nullable();
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
        Schema::dropIfExists('users');
    }
}
