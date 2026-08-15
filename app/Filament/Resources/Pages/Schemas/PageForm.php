<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Forms\Components\RichEditor\Actions\SafeAttachFilesAction;
use App\Support\SafeTemporaryUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul halaman')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                        if (blank($get('slug')) && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('subtitle')
                    ->label('Subjudul')
                    ->maxLength(255),
                RichEditor::make('body')
                    ->label('Isi halaman')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link', 'attachFiles'],
                        ['h2', 'h3', 'lead', 'paragraph'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'bulletList', 'orderedList', 'horizontalRule'],
                        ['undo', 'redo'],
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('pages/body')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsAcceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/gif',
                        'image/webp',
                        'application/octet-stream',
                    ])
                    ->saveUploadedFileAttachmentUsing(
                        fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => $file->store('pages/body', 'public')
                    )
                    ->registerActions([
                        SafeAttachFilesAction::make(),
                    ])
                    ->helperText('Sisipkan gambar: drag ke editor, atau klik ikon jepit kertas (paperclip) di samping ikon tautan. File tersimpan di storage/pages/body.'),
                FileUpload::make('hero_image')
                    ->label('Gambar header')
                    ->image()
                    ->directory('pages')
                    ->disk('public')
                    ->fetchFileInformation(false)
                    ->rules([
                        SafeTemporaryUpload::rules(['jpg', 'jpeg', 'png', 'webp', 'gif'], 3072, 'Gambar header'),
                    ])
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
