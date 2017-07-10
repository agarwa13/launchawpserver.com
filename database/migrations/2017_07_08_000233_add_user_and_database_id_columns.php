<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAndDatabaseIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('launchers', function (Blueprint $table) {
            $table->integer('wordpress_database_user_id')->nullable();
            $table->integer('wordpress_database_id')->nullable();
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
            $table->dropColumn('wordpress_database_user_id');
            $table->dropColumn('wordpress_database_id');
        });
    }
}
