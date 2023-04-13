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
        Schema::create('email_info', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('payload');
            $table->boolean('is_processed');
            $table->tinyInteger('fail_count');
            $table->boolean('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_info');
    }
};
