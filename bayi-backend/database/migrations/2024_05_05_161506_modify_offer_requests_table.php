<?php

use App\Models\OfferRequest;
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
            $table->string('request_no')->nullable()->after('from_store');
        });

        //boot creating
        $requests=OfferRequest::all();
        foreach ($requests as $request) {
            if($request->request_no) continue;
            $request->request_no = $request->createRequestNo();
            $request->save();
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_requests', function (Blueprint $table) {
            $table->dropColumn('request_no');
        });
    }
};
