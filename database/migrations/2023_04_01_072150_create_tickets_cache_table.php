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
        Schema::create('tickets_cache', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticket_id')->length(20);
            $table->text('ticket_title');
            $table->longText('ticket_description');
            $table->bigInteger('ticket_agent')->length(20);
            $table->bigInteger('ticket_status_id')->length(20);
            $table->bigInteger('ticket_brand_id')->length(20);
            $table->bigInteger('ticket_channel')->length(20);
            $table->longText('ticket_tags');
            $table->integer('ticket_agent_messages');
            $table->integer('ticket_customer_messages');
            $table->integer('ticket_total_messages');
            $table->datetime('ticket_date');
            $table->datetime('ticket_closed_date');
            $table->datetime('ticket_first_reply_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_cache');
    }
};
