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
        Schema::create('workspace_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id')->unsigned();
            $table->string('name', length: 120);
            $table->string('email', length: 80);
            $table->string('authorization_token', length: 120);
            $table->string('oauth_provider', length: 45)->nullable();
            $table->string('oauth_id', length: 100)->nullable();
            $table->string('authorization_type', length: 10);
            $table->tinyInteger('indicates_enabled');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('workspace_users', function (Blueprint $table){
            $table->index('workspace_id','fk_workspaces_users_workspaces1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_users');
    }
};
