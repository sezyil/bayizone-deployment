<?php

use App\Models\System\Language;
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
        //add language column to customer table
        Schema::table('company_customers', function (Blueprint $table) {
            $table->string('language', Language::CODE_LENGTH)->default('tr')->after('phone');

            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_customers', function (Blueprint $table) {
            $table->dropForeign(['language']);
            $table->dropColumn('language');
        });
    }
};
