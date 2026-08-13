<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addedRole = false;

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 32)->default('redaktur')->after('email');
            });
            $addedRole = true;
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $after = Schema::hasColumn('users', 'role') ? 'role' : 'email';
                $table->boolean('is_active')->default(true)->after($after);
            });
        }

        if ($addedRole) {
            // Semua akun yang sudah ada sebelum fitur role: Super Admin.
            DB::table('users')->update([
                'role' => 'super_admin',
                'is_active' => true,
            ]);
        } else {
            DB::table('users')->whereNull('role')->orWhere('role', '')->update([
                'role' => 'super_admin',
            ]);
            DB::table('users')->whereNull('is_active')->update([
                'is_active' => true,
            ]);
        }

        if (! Schema::hasTable('post_view_dailies')) {
            Schema::create('post_view_dailies', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_view_dailies');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
