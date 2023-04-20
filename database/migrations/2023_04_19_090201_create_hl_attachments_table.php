<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hl_attachments', function(Blueprint $table){

            $table->bigInteger('id')->autoIncrement();
            $table->string('attachment_url');
            $table->string('batch_number');
            $table->string('original_name');
            $table->string('attachment_name');
            $table->bigInteger('attachment_size')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('attachment_extension',50)->nullable();
            $table->dateTime('uploaded_date')->useCurrent();
            $table->dateTime('uploaded_date_gmt')->useCurrent();
            $table->tinyInteger('deleted')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hl_attachments');
    }
};