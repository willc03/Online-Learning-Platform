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
        Schema::create('section_items', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Section item details
            $table->integer('position');               // The position of the section item inside a section
            $table->string('title');                   // The item title
            $table->string('description')->nullable(); // The item description, optional
            $table->string('item_type');               // The type of item
            $table->json('item_value');                // The value of the item, json as it is variable
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('section_id');
            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete(); // Delete if the section containr is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('section_items');
    }

};
