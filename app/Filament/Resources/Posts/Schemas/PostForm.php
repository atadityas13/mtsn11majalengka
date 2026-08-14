<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten berita')
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('slug'),
                            ]),
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
                            ->fileAttachmentsDirectory('posts/body')
                            ->fileAttachmentsVisibility('public')
                            ->resizableImages()
                            ->helperText('Gunakan Enter untuk paragraf baru. Sisipkan gambar lewat ikon lampiran dan tautan lewat ikon link. Blok “Baca juga” otomatis dari berita terbaru.'),
                    ])
                    ->columns(2),
                Section::make('Publikasi')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar sampul (bisa diganti nanti)')
                            ->image()
                            ->directory('posts')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('author_name')
                            ->label('Kontributor')
                            ->maxLength(255)
                            ->default(fn (): ?string => auth()->user()?->name),
                        TextInput::make('editor_name')
                            ->label('Redaktur')
                            ->maxLength(255)
                            ->default(fn (): ?string => auth()->user()?->name),
                        TextInput::make('tags')
                            ->label('Tags')
                            ->helperText('Pisahkan dengan koma, contoh: Upacara, Nasionalisme, Kegiatan')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->native(false),
                        Toggle::make('is_published')
                            ->label('Tayangkan')
                            ->default(false),
                        TextInput::make('views_count')
                            ->label('Jumlah dilihat (awal)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Hanya Super Admin. Nilai awal saat tayang; kunjungan berikutnya tetap menambah.')
                            ->visible(function (): bool {
                                $user = Auth::user();

                                return $user instanceof User && $user->isSuperAdmin();
                            })
                            ->dehydrated(function (): bool {
                                $user = Auth::user();

                                return $user instanceof User && $user->isSuperAdmin();
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
