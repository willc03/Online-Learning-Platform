<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () : void
    {
        Schema::create('lessons', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Lesson details
            $table->string('title');                   // The lesson title
            $table->string('description')->nullable(); // The lesson description
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('section_item_id');
            $table->foreign('section_item_id')->references('id')->on('section_items')->cascadeOnDelete(); // Delete the item if the section item is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('lessons');
    }

};
