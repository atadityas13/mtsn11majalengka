<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('kemenag_logo')->nullable()->after('logo');
            $table->string('profile_video_url')->nullable()->after('youtube_url');
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->unsignedInteger('students_count')->nullable()->after('npsn');
            $table->unsignedInteger('teachers_count')->nullable()->after('students_count');
            $table->unsignedInteger('classes_count')->nullable()->after('teachers_count');
            $table->unsignedInteger('founded_year')->nullable()->after('classes_count');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'kemenag_logo',
                'profile_video_url',
                'whatsapp_number',
                'students_count',
                'teachers_count',
                'classes_count',
                'founded_year',
            ]);
        });
    }
};
