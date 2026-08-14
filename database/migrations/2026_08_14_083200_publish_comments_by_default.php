<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('comments')->where('is_approved', false)->update([
            'is_approved' => true,
        ]);
    }

    public function down(): void
    {
        // Tidak mengembalikan status moderasi lama.
    }
};
