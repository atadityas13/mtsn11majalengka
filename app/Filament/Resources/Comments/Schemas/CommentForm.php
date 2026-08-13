<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('post.title')
                    ->label('Berita')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label('Komentar')
                    ->rows(5)
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Disetujui / tampilkan di situs')
                    ->helperText('Aktifkan agar komentar muncul di halaman berita'),
            ])
            ->columns(2);
    }
}
