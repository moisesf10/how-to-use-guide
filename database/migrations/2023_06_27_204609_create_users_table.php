<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 120);
            $table->string('email', 80)->unique();
            $table->string('password', 100)->nullable();
            $table->string('oauth_provider', 20)->nullable();
            $table->string('oauth_id', 100)->nullable();
            $table->string('oauth_email', 80)->nullable();
            $table->string('timezone', 45)->nullable();
            $table->string('date_format', 45)->nullable();
            $table->string('default_language', 5)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('status', 45)->nullable();
            $table->timestamps();
        });

        // Indexes
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email', 'email_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
