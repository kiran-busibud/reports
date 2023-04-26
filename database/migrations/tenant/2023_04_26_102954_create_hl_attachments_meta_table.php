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
        Schema::create('hl_attachment_meta', function (Blueprint $table) {
            $table->bigInteger('meta_id')->autoIncrement();
            $table->bigInteger('attachment_id');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hl_attachment_meta');
    }
};
