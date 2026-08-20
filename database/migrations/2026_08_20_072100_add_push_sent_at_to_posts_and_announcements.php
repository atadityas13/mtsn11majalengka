<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('push_sent_at')->nullable()->after('published_at');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->timestamp('push_sent_at')->nullable()->after('is_published');
        });

        // Konten lama dianggap sudah "pernah dikirim" supaya tidak spam notifikasi massal.
        \Illuminate\Support\Facades\DB::table('posts')
            ->where('is_published', true)
            ->whereNull('push_sent_at')
            ->update(['push_sent_at' => now()]);

        \Illuminate\Support\Facades\DB::table('announcements')
            ->where('is_published', true)
            ->whereNull('push_sent_at')
            ->update(['push_sent_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('push_sent_at');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('push_sent_at');
        });
    }
};
