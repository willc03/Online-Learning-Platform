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
        Schema::create('section_items', function (Blueprint $table) {
            // Primary key
            $table->uuid('id')->primary();

            // Section item details
            $table->integer('position');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('item_type');
            $table->json('item_value');

            // Timestamps
            $table->timestamps();

            // Foreign keys and relations
            $table->uuid('section_id');
            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_items');
    }
};
