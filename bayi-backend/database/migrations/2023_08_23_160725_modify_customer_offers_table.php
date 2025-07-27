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
        //modify  whatsapp_notification_date,mail_notification_date columns to timestamp
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->dateTime('whatsapp_notification_date')->nullable()->default(null)->change();
            $table->dateTime('mail_notification_date')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //modify  whatsapp_notification_date,mail_notification_date columns to timestamp
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->date('whatsapp_notification_date')->nullable()->change();
            $table->date('mail_notification_date')->nullable()->change();
        });
    }
};
