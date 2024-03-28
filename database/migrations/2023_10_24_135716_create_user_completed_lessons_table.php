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
        Schema::create('user_completed_lessons', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Other keys
            $table->integer('score'); // The XP score, calculated in Lesson controller
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete(); // Delete if the lesson is deleted
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete(); // Delete if the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('user_completed_lessons');
    }

};
