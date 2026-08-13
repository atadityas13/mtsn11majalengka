<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        User::query()->orderBy('id')->each(function (User $user): void {
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
                User::query()
                    ->where('username', $username)
                    ->whereKeyNot($user->id)
                    ->exists()
            ) {
                $username = $base.$suffix;
                $suffix++;
            }

            $user->forceFill(['username' => $username])->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
