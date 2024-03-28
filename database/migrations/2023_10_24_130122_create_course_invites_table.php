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
        Schema::create('course_invites', function ( Blueprint $table ) {
            // Primary key
            $table->id();
            // Course invite details
            $table->uuid('invite_id');                    // A unique identifier for the invite for URL use
            $table->boolean('is_active')->default(true);  // Set invites as active by fault
            $table->timestamp('expiry_date')->nullable(); // Expiry date holder, can be made null for permanent invites
            $table->integer('uses')->default(0);          // Uses, set to 0 when created.
            $table->integer('max_uses')->nullable();      // Max uses, can be made null for infinite uses.
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete(); // Delete the invite if the course is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('course_invites');
    }

};
