<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Fillable(['name', 'username', 'email', 'avatar', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        // Belum migrate role/is_active: izinkan akses supaya login tidak 500.
        if (! Schema::hasColumn($this->getTable(), 'role')) {
            return true;
        }

        try {
            if (Schema::hasColumn($this->getTable(), 'is_active') && ! $this->is_active) {
                return false;
            }

            return $this->role instanceof UserRole;
        } catch (Throwable) {
            return false;
        }
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->avatar)) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    public function isSuperAdmin(): bool
    {
        if (! Schema::hasColumn($this->getTable(), 'role')) {
            return true;
        }

        try {
            return $this->role === UserRole::SuperAdmin;
        } catch (Throwable) {
            return false;
        }
    }

    public function isRedaktur(): bool
    {
        if (! Schema::hasColumn($this->getTable(), 'role')) {
            return false;
        }

        try {
            return $this->role === UserRole::Redaktur;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }
}
