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
        Schema::create('ticket_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('ticket_title');
            $table->longText('ticket_description');
            $table->bigInteger('ticket_agent')->length(20);
            $table->bigInteger('ticket_status_id')->length(20);
            $table->bigInteger('ticket_brand_id')->length(20);
            $table->bigInteger('ticket_channel')->length(20);
            $table->longText('ticket_tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_reports');
    }
};
