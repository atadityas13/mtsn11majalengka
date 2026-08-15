<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Filament\Forms\Components\RichEditor\Actions\SafeAttachFilesAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Isi pengumuman')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link'],
                        ['h2', 'h3', 'lead', 'paragraph'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'bulletList', 'orderedList', 'horizontalRule'],
                        ['attachFiles'],
                        ['undo', 'redo'],
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('announcements/body')
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
                        fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => $file->store('announcements/body', 'public')
                    )
                    ->registerActions([
                        SafeAttachFilesAction::make(),
                    ])
                    ->helperText('Sisipkan gambar lewat ikon klip. File tersimpan di storage/announcements/body.'),
                DatePicker::make('published_on')
                    ->label('Tanggal')
                    ->native(false)
                    ->default(now()),
                Toggle::make('is_pinned')
                    ->label('Sematkan di atas')
                    ->default(false),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
