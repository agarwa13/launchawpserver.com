<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forge_site_id')->nullable();
            $table->string('domain_name');
            $table->string('forge_database_id')->nullable();
            $table->string('forge_database_user_id')->nullable();
            $table->string('database_name')->nullable();
            $table->string('database_user_name')->nullable();
            $table->string('database_user_password')->nullable();
            $table->integer('server_id');
            $table->string('status');
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
        Schema::dropIfExists('sites');
    }
}
