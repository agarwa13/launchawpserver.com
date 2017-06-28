<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaunchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('launchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aws_access_key_id');
            $table->string('aws_secret_access_key');
            $table->string('server_name');
            $table->string('server_size');
            $table->string('region');
            $table->string('php_version');
            $table->string('domain_name');
            $table->string('email_address');
            $table->string('key_pair_location')->nullable();
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
        Schema::dropIfExists('launchers');
    }
}
