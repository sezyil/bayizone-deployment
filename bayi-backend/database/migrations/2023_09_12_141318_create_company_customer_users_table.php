<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_customer_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->uuid('company_customer_id')->nullable();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('language', 20)->default('tr');

            $table->boolean('password_need_change')->default(false);
            $table->boolean('status')->default(1);
            $table->rememberToken();
            $table->timestamps();


            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
            $table->foreign('company_customer_id')->references('id')->on('company_customers')->onDelete('cascade');
            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customer_users');
    }
};
