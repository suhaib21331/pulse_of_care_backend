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
        Schema::create('elderlies', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->text('chronic_diseases');
            $table->text('current_medications');
            $table->text('allergies');
            $table->boolean('need_wheel_chair')->default(false);
            $table->boolean('uses_diapers')->default(false);
            $table->string('movement_level');
            $table->string('city');
            $table->text('detailed_address');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elderlies');
    }
};
