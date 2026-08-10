<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten berita')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textarea::make('excerpt')
                            ->label('Cuplikan')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Isi berita')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Publikasi')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar sampul')
                            ->image()
                            ->directory('posts')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('author_name')
                            ->label('Penulis')
                            ->maxLength(255),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->native(false),
                        Toggle::make('is_published')
                            ->label('Tayangkan')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
