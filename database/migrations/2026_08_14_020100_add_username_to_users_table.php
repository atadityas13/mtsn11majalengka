<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('name');
            });
        }

        $users = DB::table('users')->orderBy('id')->get(['id', 'email', 'username']);

        foreach ($users as $user) {
            if (filled($user->username)) {
                continue;
            }

            $base = Str::of((string) $user->email)
                ->before('@')
                ->lower()
                ->replaceMatches('/[^a-z0-9._-]/', '')
                ->value();

            if ($base === '') {
                $base = 'user'.$user->id;
            }

            $username = $base;
            $suffix = 1;

            while (
                DB::table('users')
                    ->where('username', $username)
                    ->where('id', '!=', $user->id)
                    ->exists()
            ) {
                $username = $base.$suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }
    }
};
