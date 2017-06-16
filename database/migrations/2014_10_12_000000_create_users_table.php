<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('email', 50)->unique();
            $table->string('phone', 20);
            $table->string('mobile', 20);
            $table->string('address', 100);
            $table->string('password');
            $table->enum('role', ['SuperAdmin', 'Admin', 'User', 'TeamMember'])->default('TeamMember');
            $table->integer('display_order')->default(0);
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
