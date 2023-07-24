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
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_topic_id')->unsigned();
            $table->string('uid', length: 75);
            $table->string('page_name', length: 60);
            $table->json('content')->nullable();
            $table->json('screenshot')->nullable();
            $table->integer('order');
            $table->string('status', length: 20)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('pages', function (Blueprint $table){
            $table->unique(['page_name', 'workspace_topic_id'], 'page_name_UNIQUE');
            $table->index('workspace_topic_id', 'fk_pages_workspace_topics1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
