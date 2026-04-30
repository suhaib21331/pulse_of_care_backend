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
        Schema::create('service_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->uuid('provider_id');
            $table->enum('provider_type', ['nurse', 'driver', 'companion']);
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('matching_score', 8, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_assignments');
    }
};
