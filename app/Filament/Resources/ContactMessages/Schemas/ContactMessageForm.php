<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
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
                TextInput::make('phone')
                    ->label('Telepon')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('subject')
                    ->label('Subjek')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('message')
                    ->label('Pesan')
                    ->rows(6)
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                DateTimePicker::make('read_at')
                    ->label('Dibaca pada')
                    ->native(false),
            ])
            ->columns(2);
    }
}
