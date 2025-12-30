<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->string('position')->default('homepage_banner');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('priority')->default(0);
            $table->json('target_pages')->nullable();
            $table->json('target_categories')->nullable();
            $table->json('target_products')->nullable();
            $table->boolean('show_on_all_pages')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('position');
            $table->index('status');
            $table->index('priority');
            $table->index(['start_at', 'end_at']);
        });

        Schema::create('banner_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banners')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->morphs('clickable');
            $table->timestamp('clicked_at');
            
            $table->index('banner_id');
            $table->index('clicked_at');
            $table->index(['clickable_type', 'clickable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_clicks');
        Schema::dropIfExists('banners');
    }
};
