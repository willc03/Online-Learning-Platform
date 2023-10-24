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
        Schema::create('lesson_items', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('position');
            $table->string('item_title');
            $table->string('description')->nullable();
            $table->string('item_type');
            $table->json('item_value');
            $table->timestamps();

            $table->uuid('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_items');
    }
};
