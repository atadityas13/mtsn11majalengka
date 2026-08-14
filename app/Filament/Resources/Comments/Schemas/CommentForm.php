<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Placeholder;
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
                Placeholder::make('post_title')
                    ->label('Berita')
                    ->content(fn ($record) => $record?->post?->title ?: '—')
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label('Komentar')
                    ->rows(5)
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Tampilkan di situs')
                    ->helperText('Nonaktifkan untuk menyembunyikan komentar dari halaman berita tanpa menghapusnya'),
            ])
            ->columns(2);
    }
}
