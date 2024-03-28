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
        Schema::create('courses', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Course detail fields
            $table->string('title');                                                    // Course title
            $table->string('description')->nullable();                                  // Description, optional
            $table->boolean('is_public');                                               // Flag for course publicity
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->unsignedBigInteger('owner');                                        // Store a bigint for the user id
            $table->foreign('owner')->references('id')->on('users')->cascadeOnDelete(); // Delete the course when the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('courses');
    }

};
