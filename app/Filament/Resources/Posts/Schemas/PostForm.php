<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\User;
use App\Support\SafeTemporaryUpload;
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
    /**
     * @return list<string>
     */
    public static function imageExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    }

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
                            ->preload(),
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
                            ->maxLength(1000)
                            ->helperText('Ringkasan singkat untuk daftar berita. Maksimal 1000 karakter.')
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
                                ['undo', 'redo'],
                            ])
                            // Lampiran gambar di editor dinonaktifkan sementara agar create berita stabil di hosting.
                            ->fileAttachments(false)
                            ->helperText('Gunakan Enter untuk paragraf baru. Gambar sampul diisi di kanan. “Baca juga” otomatis: berita sebelumnya & sesudahnya di antar paragraf, berita terbaru di akhir.'),
                    ])
                    ->columns(2),
                Section::make('Publikasi')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar sampul (bisa diganti nanti)')
                            ->disk('public')
                            ->directory('posts')
                            ->visibility('public')
                            ->fetchFileInformation(false)
                            // Jangan pakai maxSize(): di hosting sering crash UnableToRetrieveMetadata
                            // saat file livewire-tmp sudah kedaluwarsa / hilang.
                            ->rules([
                                SafeTemporaryUpload::rules(self::imageExtensions(), 3072, 'Gambar sampul'),
                            ])
                            ->helperText('Format: JPG, PNG, WEBP, atau GIF. Maksimal 3 MB. Jika form lama terbuka, unggah ulang sampul sebelum simpan.')
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
                            ->helperText('Pisahkan dengan koma, contoh: Pramuka, Jambore Ranting, Prestasi')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->native(true)
                            ->seconds(false)
                            ->timezone('Asia/Jakarta')
                            ->default(fn () => now('Asia/Jakarta'))
                            ->helperText('Tanggal terbit ditentukan dari field ini (WIB), bukan dari tombol Tayangkan.'),
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
