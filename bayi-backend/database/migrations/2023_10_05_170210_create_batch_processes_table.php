<?php

use App\Enums\BatchProcessesTypesEnum;
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

        Schema::create('batch_processes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->text('payload');
            $table->text('errors')->nullable();
            $table->enum('type', BatchProcessesTypesEnum::allValues());
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_failed')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_processes');
    }
};
