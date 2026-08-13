<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('achievements')) {
            DB::table('achievements')
                ->where('description', 'like', '%panel admin%')
                ->update([
                    'description' => 'Prestasi yang diraih peserta didik dan madrasah.',
                    'updated_at' => now(),
                ]);
        }

        if (Schema::hasTable('videos')) {
            DB::table('videos')
                ->where('description', 'like', '%panel admin%')
                ->where('type', 'short')
                ->update([
                    'description' => 'Cuplikan singkat kegiatan pembelajaran dan keagamaan di madrasah.',
                    'updated_at' => now(),
                ]);

            DB::table('videos')
                ->where('description', 'like', '%panel admin%')
                ->where('type', 'video')
                ->update([
                    'description' => 'Video dokumentasi profil dan kegiatan madrasah.',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};
