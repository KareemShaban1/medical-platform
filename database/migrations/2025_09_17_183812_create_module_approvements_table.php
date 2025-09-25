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
        Schema::create('module_approvements', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('module');
            $table->foreignId('action_by')->constrained('admins')->cascadeOnDelete();
            $table->enum('action', [ 'pending', 'under_review', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_approvements');
    }
};
