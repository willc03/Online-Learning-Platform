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
            $table->id();
            $table->uuid('invite_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->boolean('is_active');
            $table->date('expiry_date');
            $table->integer('max_uses')->nullable();
            $table->timestamps();
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
