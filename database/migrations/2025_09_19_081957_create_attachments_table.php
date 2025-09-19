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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->ulid();
            $table->morphs('attachable');
            $table->string('name');
            $table->tinyInteger('type')->default(1);
            $table->integer('size');
            $table->string('disk');
            $table->string('path');
            $table->string('url');
            $table->string('mime_type');
            $table->integer('created_by');
            $table->dateTime('expired_at')->nullable();
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
