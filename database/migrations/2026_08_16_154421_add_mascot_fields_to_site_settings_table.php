<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('mascot_enabled')->default(false)->after('userway_account_id');
            $table->string('mascot_theme')->default('default')->after('mascot_enabled');
            $table->text('mascot_message')->nullable()->after('mascot_theme');
            $table->date('mascot_starts_on')->nullable()->after('mascot_message');
            $table->date('mascot_ends_on')->nullable()->after('mascot_starts_on');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'mascot_enabled',
                'mascot_theme',
                'mascot_message',
                'mascot_starts_on',
                'mascot_ends_on',
            ]);
        });
    }
};
