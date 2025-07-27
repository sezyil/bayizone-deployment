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
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->uuid('role_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('language', 20)->default('tr');

            $table->boolean('password_need_change')->default(false);
            $table->boolean('status')->default(1);
            $table->rememberToken();
            $table->timestamps();


            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
};
