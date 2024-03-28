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
        Schema::create('user_courses', function ( Blueprint $table ) {
            // Primary key
            $table->id();
            // User course details
            $table->boolean('blocked')->default(false); // Flag for users blocked from a course.
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->unsignedBigInteger('user_id');
            $table->uuid('course_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();     // Delete the user course record if the user is deleted
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete(); // Delete the user course record if the course is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('user_courses');
    }

};
