<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_upgrade_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs('requestable');
            $table->foreignId('current_plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->foreignId('requested_plan_id')->constrained('plans')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->morphs('processed_by');
            $table->timestamps();

            // $table->index(['requestable_type', 'requestable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_upgrade_requests');
    }
};

