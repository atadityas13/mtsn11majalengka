<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return $data;
        }

        if (blank($data['author_name'] ?? null)) {
            $data['author_name'] = $user->name;
        }

        if (blank($data['editor_name'] ?? null)) {
            $data['editor_name'] = $user->name;
        }

        return $data;
    }
}
