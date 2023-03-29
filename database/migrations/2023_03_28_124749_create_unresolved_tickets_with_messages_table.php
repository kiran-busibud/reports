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
        Schema::create('unresolved_tickets', function (Blueprint $table) {
            $table->id();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unresolved_tickets_with_messages');
    }
};
