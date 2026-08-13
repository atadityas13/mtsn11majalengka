<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('location')
                ->constrained('menu_items')
                ->nullOnDelete();
        });

        $profil = DB::table('menu_items')
            ->where('location', 'header')
            ->where('label', 'Profil')
            ->whereNull('parent_id')
            ->first();

        if ($profil) {
            DB::table('menu_items')
                ->where('location', 'header')
                ->whereIn('label', ['Struktur Organisasi', 'Guru & Tendik'])
                ->update([
                    'parent_id' => $profil->id,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
