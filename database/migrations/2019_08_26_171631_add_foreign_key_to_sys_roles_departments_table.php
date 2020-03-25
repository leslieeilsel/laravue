<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToSysRolesDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_roles_departments', function (Blueprint $table) {
            // 添加外键
            $table->foreign('department_id')->references('id')->on('sys_departments');
            $table->foreign('role_id')->references('id')->on('sys_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_roles_departments', function (Blueprint $table) {
            $table->dropForeign('sys_roles_departments_department_id_foreign');
            $table->dropForeign('sys_roles_departments_role_id_foreign');
        });
    }
}
