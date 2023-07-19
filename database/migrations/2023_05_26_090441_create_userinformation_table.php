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
        Schema::create('userinformations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phoneNumber')->default('');
            $table->string('address')->nullable();
            $table->string('gender')->nullable();
            $table->string('descriptions')->nullable();
            $table->string('avatar')->nullable();
            $table->string('background')->nullable();

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
        Schema::table('userinformations', function (Blueprint $table) {
            // Xóa liên kết khóa ngoại và cột role_id
            $table->dropForeign(['idUser']);
            $table->dropColumn('idUser');
            
        });
        Schema::dropIfExists('userinformations');
    }
};
