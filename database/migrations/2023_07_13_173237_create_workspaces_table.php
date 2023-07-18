<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('uid', 65);
            $table->string('name', 60);
            $table->string('description', 300)->nullable();
            $table->tinyInteger('indicates_public_access');
            $table->tinyInteger('indicates_enabled');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        // Indexes
        Schema::table('workspaces', function (Blueprint $table) {
            $table->index('user_id','fk_workspace_users_idx');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
