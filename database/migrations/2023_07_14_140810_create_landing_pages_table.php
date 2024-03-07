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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id')->unsigned();
            $table->string('uid', length: 60);
            $table->string('name', length: 45);
            $table->string('uri', length: 200);
            $table->json('content');
            $table->json('screenshot')->nullable();
            $table->integer('order');
            $table->string('status', length: 20);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('landing_pages', function (Blueprint $table){
            $table->unique(['name','workspace_id'], 'name_UNIQUE');
            $table->index('workspace_id','fk_landing_pages_workspaces1_idx');
            $table->unique(['uri','workspace_id'],'uri_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
