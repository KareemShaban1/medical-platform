<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Text content positioning
            $table->enum('text_position', [
                'top-left',
                'top-center',
                'top-right',
                'center-left',
                'center',
                'center-right',
                'bottom-left',
                'bottom-center',
                'bottom-right',
                'custom',
            ])->default('center')->after('content');

            // Custom text position (for custom positioning)
            $table->json('text_position_custom')->nullable()->after('text_position'); // {top: '10%', left: '20%'}

            // Button positioning (relative to text or independent)
            $table->enum('button_position', [
                'below-text',
                'above-text',
                'left-of-text',
                'right-of-text',
                'custom',
            ])->default('below-text')->after('text_position_custom');

            // Custom button position
            $table->json('button_position_custom')->nullable()->after('button_position'); // {top: '80%', left: '50%'}

            // Text styling options
            $table->string('text_color')->default('#ffffff')->after('button_position_custom');
            $table->string('text_background_color')->nullable()->after('text_color');
            $table->integer('text_background_opacity')->default(0)->after('text_background_color'); // 0-100
            $table->string('text_alignment')->default('left')->after('text_background_opacity'); // left, center, right

            // Button styling
            $table->string('button_color')->default('#007bff')->after('text_alignment');
            $table->string('button_text_color')->default('#ffffff')->after('button_color');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'text_position',
                'text_position_custom',
                'button_position',
                'button_position_custom',
                'text_color',
                'text_background_color',
                'text_background_opacity',
                'text_alignment',
                'button_color',
                'button_text_color',
            ]);
        });
    }
};
