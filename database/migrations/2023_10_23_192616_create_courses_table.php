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
        Schema::create('courses', function (Blueprint $table) {
            // Primary key
            $table->uuid('id')->primary();

            // Course detail fields
            $table->string('title');
            $table->string('description')->nullable();
            $table->boolean('is_public');

            // Timestamps
            $table->timestamps();

            // Foreign keys and relations
            $table->unsignedBigInteger('owner');
            $table->foreign('owner')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
