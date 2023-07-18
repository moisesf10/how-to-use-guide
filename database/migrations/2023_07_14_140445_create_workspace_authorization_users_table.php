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
        Schema::create('workspace_authorization_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_user_id')->unsigned();
            $table->bigInteger('workspace_topic_id')->unsigned();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('workspace_authorization_users', function (Blueprint $table){
            $table->index('workspace_user_id', 'fk_workspace_authorization_users_workspace_users1_idx');
            $table->index('workspace_topic_id','fk_workspace_authorization_users_workspace_topics1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_authorization_users');
    }
};
