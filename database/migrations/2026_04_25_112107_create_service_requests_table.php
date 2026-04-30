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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('elder_id')->constrained('users')->cascadeOnDelete();
            $table->enum('service_type', ['nurse', 'driver', 'companion']);
            $table->enum('service_condition', ['normal', 'urgent', 'emergency'])->default('normal');
            $table->string('service_address')->nullable();
            $table->decimal('service_latitude', 10, 7)->nullable();
            $table->decimal('service_longitude', 10, 7)->nullable();
            $table->enum('status', ['pending', 'assigned', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
