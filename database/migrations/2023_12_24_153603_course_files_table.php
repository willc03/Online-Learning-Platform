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
        Schema::create('course_files', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Course file details
            $table->string('name'); // The public name of the file
            $table->string('path'); // The stored path of the file
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete(); // Delete if the course is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('course_files');
    }

};
