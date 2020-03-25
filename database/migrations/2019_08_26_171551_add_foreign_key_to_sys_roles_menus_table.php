<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToSysRolesMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_roles_menus', function (Blueprint $table) {
            // 添加外键
            $table->foreign('menu_id')->references('id')->on('sys_menus');
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
        Schema::table('sys_roles_menus', function (Blueprint $table) {
            $table->dropForeign('sys_roles_menus_menu_id_foreign');
            $table->dropForeign('sys_roles_menus_role_id_foreign');
        });
    }
}
