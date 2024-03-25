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
        Schema::create('user_completed_lessons', function (Blueprint $table) {
            // Primary key
            $table->uuid('id')->primary();

            // Other keys
            $table->integer('score');

            // Timestamps
            $table->timestamps();

            // Foreign keys and relations
            $table->uuid('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_completed_lessons');
    }
};
