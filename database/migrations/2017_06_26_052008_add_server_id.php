<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('launchers', function (Blueprint $table) {
            $table->integer('forge_server_id')->nullable();
            $table->string('forge_username')->nullable();
            $table->string('database_username')->nullable();
            $table->string('database_password')->nullable();
            $table->string('sudo_password')->nullable();
            $table->string('wordpress_database_name')->nullable();
            $table->string('wordpress_database_user')->nullable();
            $table->string('wordpress_database_password')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('launchers', function (Blueprint $table) {
            $table->dropColumn('forge_server_id');
            $table->dropColumn('forge_username');
            $table->dropColumn('database_username');
            $table->dropColumn('database_password');
            $table->dropColumn('sudo_password');
            $table->dropColumn('wordpress_database_name');
            $table->dropColumn('wordpress_database_user');
            $table->dropColumn('wordpress_database_password');
        });
    }
}
