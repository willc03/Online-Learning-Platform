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
        Schema::create('users', function ( Blueprint $table ) {
            // Primary key
            $table->id();
            // User details
            $table->string('name');                     // User's name, concatenated in the controller.
            $table->string('email')->unique();          // Ensure emails are not repeatable.
            $table->string('username', '20')->unique(); // Ensures usernames are not repeatable.
            $table->string('password');                 // Password, hashed.
            $table->rememberToken();                    // Allow user to stay logged in for prolonged periods.
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('users');
    }

};
