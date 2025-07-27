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
        //modify plans table
        Schema::table('plans', function (Blueprint $table) {
            //drop catalog_management
            $table->dropColumn('catalog_management');
            //proforma_invoices
            $table->dropColumn('proforma_invoices');
            //d create_proposal
            $table->dropColumn('create_proposal');
            //proposal_management
            $table->dropColumn('proposal_management');
            //whatsapp_integration
            $table->dropColumn('whatsapp_integration');
            //send_proposal_with_link
            $table->dropColumn('send_proposal_with_link');

            //new columns
            //online_store
            $table->boolean('online_store')->default(false);
            //product_count
            $table->integer('product_count')->default(0);
            //provider_panel_count
            $table->integer('provider_panel_count')->default(0);

        });

        //modify subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            //drop catalog_management
            $table->dropColumn('catalog_management');
            //proforma_invoices
            $table->dropColumn('proforma_invoices');
            //d create_proposal
            $table->dropColumn('create_proposal');
            //proposal_management
            $table->dropColumn('proposal_management');
            //whatsapp_integration
            $table->dropColumn('whatsapp_integration');
            //send_proposal_with_link
            $table->dropColumn('send_proposal_with_link');

            //new columns
            //online_store
            $table->boolean('online_store')->default(false);
            //product_count
            $table->integer('product_count')->default(0);
            //left_product_count
            $table->integer('left_product_count')->default(0);
            //provider_panel_count
            $table->integer('provider_panel_count')->default(0);
            //left_provider_panel_count
            $table->integer('left_provider_panel_count')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //rollback plans table
        Schema::table('plans', function (Blueprint $table) {
            //add columns
            $table->boolean('catalog_management')->default(false);
            $table->boolean('proforma_invoices')->default(false);
            $table->boolean('create_proposal')->default(false);
            $table->boolean('proposal_management')->default(false);
            $table->boolean('whatsapp_integration')->default(false);
            $table->boolean('send_proposal_with_link')->default(false);


            //drop columns
            $table->dropColumn('online_store');
            $table->dropColumn('product_count');
            $table->dropColumn('provider_panel_count');
        });

        //rollback subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            //add columns
            $table->boolean('catalog_management')->default(false);
            $table->boolean('proforma_invoices')->default(false);
            $table->boolean('create_proposal')->default(false);
            $table->boolean('proposal_management')->default(false);
            $table->boolean('whatsapp_integration')->default(false);
            $table->boolean('send_proposal_with_link')->default(false);
            //drop columns
            $table->dropColumn('online_store');
            $table->dropColumn('product_count');
            $table->dropColumn('left_product_count');
            $table->dropColumn('provider_panel_count');
            $table->dropColumn('left_provider_panel_count');
        });
    }
};
