<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userinformations', function (Blueprint $table) {
            Schema::table('userinformations', function (Blueprint $table) {
                $table->unsignedBigInteger('idUser')->nullable();
                $table->foreign('idUser')->references('id')->on('users');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('userinformations', function (Blueprint $table) {
            //
        });
    }
};
