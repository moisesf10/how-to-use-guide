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
        Schema::create('tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dastination_table_id')->nullable()->unsigned();
            $table->string('destination_table', length: 300)->nullable();
            $table->string('token', length: 100)->unique();
            $table->json('content')->nullable();
            $table->tinyInteger('indicates_enable');
            $table->dateTime('expires_in')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('tokens', function (Blueprint $table){
            $table->unique('token','token_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
