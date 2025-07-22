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
        Schema::table('events', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('status');
            $table->string('background_image_path')->nullable()->after('logo_path');
            $table->string('theme_color', 7)->nullable()->after('background_image_path');
            $table->text('custom_css')->nullable()->after('theme_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'background_image_path',
                'theme_color',
                'custom_css'
            ]);
        });
    }
}; 