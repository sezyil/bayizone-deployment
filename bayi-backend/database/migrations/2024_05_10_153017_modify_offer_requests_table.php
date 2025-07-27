<?php

use App\Models\System\Currency;
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
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->string('currency', Currency::CODE_LENGTH)->default('tl')->after('global_note');

            $table->foreign('currency')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->dropForeign(['currency']);
            $table->dropColumn('currency');
        });
    }
};
