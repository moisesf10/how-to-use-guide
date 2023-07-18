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
        Schema::create('workspace_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id')->unsigned();
            $table->string('name', length: 60);
            $table->tinyInteger('indicates_enabled');
            $table->integer('order')->unsigned();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('workspace_blocks', function (Blueprint $table){
            $table->index('workspace_id', 'fk_workspace_blocks_workspaces1_idx');
            $table->unique(['name','workspace_id'],'name_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_blocks');
    }
};
