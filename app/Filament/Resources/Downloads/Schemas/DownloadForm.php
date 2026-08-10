<?php

namespace App\Filament\Resources\Downloads\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DownloadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('file_path')
                    ->label('File (PDF/DOC/ZIP)')
                    ->required()
                    ->directory('downloads')
                    ->disk('public')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/zip',
                        'image/*',
                    ])
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->label('Kategori')
                    ->placeholder('PPDB / Kurikulum / Lainnya'),
                TextInput::make('download_count')
                    ->label('Jumlah unduhan')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
