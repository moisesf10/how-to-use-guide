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
        Schema::create('workspace_editors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id')->unsigned();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->string('email', length: 80)->unique();
            $table->string('name', length: 120);
            $table->string('status_send_mail', length: 20)->nullable();
            $table->dateTime('last_attemp_send_mail')->nullable();
            $table->string('mail_error', length: 300)->nullable();
            $table->string('status', length: 25);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::table('workspace_editors', function (Blueprint $table){
            $table->unique('email', 'email_UNIQUE');
            $table->index('user_id','fk_workspace_editors_users1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_editors');
    }
};
