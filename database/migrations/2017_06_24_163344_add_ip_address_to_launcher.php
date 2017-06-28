<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpAddressToLauncher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('launchers', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->string('instance_id')->nullable();
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
            $table->dropColumn('ip_address');
            $table->dropColumn('instance_id');
        });
    }
}
