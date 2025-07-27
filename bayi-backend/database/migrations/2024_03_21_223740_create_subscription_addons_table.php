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
        Schema::create('subscription_addons', function (Blueprint $table) {
            $table->id();
            //subscription id
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            //addon id
            $table->foreignUuid('addon_id')->nullable()->constrained('plan_addons')->nullOnDelete();
            //type of the addon
            $table->string('type');
            //name of the addon
            $table->string('name_of_addon');
            //is the addon boolean
            $table->boolean('is_boolean')->default(false);
            //quantity of the addon
            $table->integer('quantity');
            //price of the addon
            $table->decimal('price', 10, 2);
            //status of the addon
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_addons');
    }
};
