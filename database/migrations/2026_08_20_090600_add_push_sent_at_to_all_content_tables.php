<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    protected array $tables = [
        'agendas',
        'gallery_items',
        'achievements',
        'downloads',
        'videos',
        'pages',
        'staff_members',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'push_sent_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->timestamp('push_sent_at')->nullable()->after('is_published');
            });

            // Konten lama: jangan spam notifikasi massal.
            DB::table($table)
                ->where('is_published', true)
                ->whereNull('push_sent_at')
                ->update(['push_sent_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'push_sent_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('push_sent_at');
            });
        }
    }
};
