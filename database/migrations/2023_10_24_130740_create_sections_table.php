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
        Schema::create('sections', function ( Blueprint $table ) {
            // Primary key
            $table->uuid('id')->primary();
            // Section details
            $table->integer('position');               // The position of the section on the view
            $table->string('title');                   // The section title
            $table->string('description')->nullable(); // The section description, optional
            // Timestamps
            $table->timestamps();
            // Foreign keys and relations
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete(); // Delete the section if the course is deleted.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('sections');
    }

};
