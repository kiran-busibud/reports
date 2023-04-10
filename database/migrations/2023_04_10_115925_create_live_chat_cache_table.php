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
        Schema::create('live_chats_cache', function (Blueprint $table) {
            $table->bigInteger('id')->length(20);
            $table->text('assigned_agent_id');
            $table->dateTime('created_at');
            $table->integer('message_count');
            $table->integer('resolved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_chat_caches');
    }
};
