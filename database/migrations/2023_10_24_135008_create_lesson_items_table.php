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
        Schema::create('lesson_items', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Lesson item details
            $table->integer('position');               // The position of the item in the lesson
            $table->string('item_title');              // The title of the item
            $table->string('description')->nullable(); // The item description, optional
            $table->string('item_type');               // The item type
            $table->json('item_value');                // The item value, json as it is variable
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete(); // Delete if the lesson is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('lesson_items');
    }

};
