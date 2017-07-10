<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('size');
            $table->string('region');
            $table->string('php_version');
            $table->string('key_pair_location')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('private_ip_address')->nullable();
            $table->integer('forge_server_id')->nullable();
            $table->string('forge_username')->nullable();
            $table->string('database_username')->nullable();
            $table->string('database_password')->nullable();
            $table->string('aws_instance_id')->nullable();
            $table->string('status')->nullable();
            $table->integer('credential_id')->nullable();
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
        Schema::dropIfExists('servers');
    }
}
