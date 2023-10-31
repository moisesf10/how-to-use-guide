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
        Schema::create('workspace_topics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_block_id')->unsigned();
            $table->string('name', length: 60);
            $table->string('language', length: 10);
            $table->tinyInteger('indicates_sublevel');
            $table->string('icon', length: 4000)->nullable();
            $table->integer('order')->unsigned();
            $table->tinyInteger('indicates_enabled');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('workspace_topics', function (Blueprint $table){
            $table->index('workspace_block_id','fk_workspace_topics_workspace_blocks1_idx');
            $table->unique(['name','workspace_block_id','language'], 'name_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_topics');
    }
};
