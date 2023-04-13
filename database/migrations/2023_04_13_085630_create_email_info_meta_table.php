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
        Schema::create('email_info_meta', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('email_info_id')->length(20);
            $table->char('meta_key')->length(255);
            $table->longText('meta_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_info_meta');
    }
};
