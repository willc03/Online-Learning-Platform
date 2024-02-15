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
        Schema::create('course_invites', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Course invite details
            $table->uuid('invite_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expiry_date');
            $table->integer('uses')->default(0);
            $table->integer('max_uses')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign keys and relations
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_invites');
    }
};
