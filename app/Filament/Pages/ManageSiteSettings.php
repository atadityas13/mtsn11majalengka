<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs & Tampilan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('Pengaturan')
                        ->tabs([
                            Tab::make('Identitas')
                                ->schema([
                                    TextInput::make('school_name')
                                        ->label('Nama madrasah')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('tagline')
                                        ->label('Tagline')
                                        ->maxLength(255),
                                    TextInput::make('npsn')
                                        ->label('NPSN')
                                        ->maxLength(50),
                                    TextInput::make('accreditation_label')
                                        ->label('Label akreditasi')
                                        ->maxLength(100),
                                    TextInput::make('accreditation_value')
                                        ->label('Nilai akreditasi')
                                        ->maxLength(50),
                                    FileUpload::make('accreditation_image')
                                        ->label('Gambar surat akreditasi')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public')
                                        ->helperText('Tampil di bagian bawah beranda')
                                        ->columnSpanFull(),
                                    TextInput::make('founded_year')
                                        ->label('Tahun berdiri')
                                        ->numeric(),
                                    TextInput::make('students_count')
                                        ->label('Jumlah siswa')
                                        ->numeric(),
                                    TextInput::make('teachers_count')
                                        ->label('Jumlah guru')
                                        ->numeric(),
                                    TextInput::make('classes_count')
                                        ->label('Jumlah rombel')
                                        ->numeric(),
                                    FileUpload::make('logo')
                                        ->label('Logo madrasah (bisa diganti)')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public'),
                                    FileUpload::make('kemenag_logo')
                                        ->label('Logo Kemenag (bisa diganti)')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public'),
                                    FileUpload::make('favicon')
                                        ->label('Favicon')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public'),
                                    TextInput::make('primary_color')
                                        ->label('Warna utama')
                                        ->default('#0a7a3e'),
                                    TextInput::make('accent_color')
                                        ->label('Warna aksen')
                                        ->default('#d4a017'),
                                ])
                                ->columns(2),
                            Tab::make('Hero Beranda')
                                ->schema([
                                    TextInput::make('hero_title')
                                        ->label('Judul hero')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Textarea::make('hero_subtitle')
                                        ->label('Subjudul hero')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    FileUpload::make('hero_image')
                                        ->label('Gambar hero')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public')
                                        ->columnSpanFull(),
                                    TextInput::make('hero_cta_label')
                                        ->label('Teks tombol CTA'),
                                    TextInput::make('hero_cta_url')
                                        ->label('URL tombol CTA'),
                                ])
                                ->columns(2),
                            Tab::make('Sambutan')
                                ->schema([
                                    TextInput::make('headmaster_name')
                                        ->label('Nama kepala madrasah'),
                                    TextInput::make('headmaster_title')
                                        ->label('Jabatan'),
                                    FileUpload::make('headmaster_photo')
                                        ->label('Foto')
                                        ->image()
                                        ->directory('settings')
                                        ->disk('public')
                                        ->columnSpanFull(),
                                    Textarea::make('headmaster_quote')
                                        ->label('Kutipan / sambutan')
                                        ->rows(4)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                            Tab::make('Kontak & Layanan')
                                ->schema([
                                    Textarea::make('address')
                                        ->label('Alamat')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                    TextInput::make('phone')->label('Telepon'),
                                    TextInput::make('whatsapp_number')
                                        ->label('WhatsApp (628...)')
                                        ->helperText('Format internasional tanpa +, contoh 6281234567890'),
                                    TextInput::make('email')->label('Email')->email(),
                                    Textarea::make('map_embed_url')
                                        ->label('URL embed peta')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                    TextInput::make('ppdb_url')->label('URL PPDB'),
                                    TextInput::make('rdm_url')->label('URL RDM'),
                                    TextInput::make('kemenag_url')->label('URL Kemenag'),
                                    TextInput::make('facebook_url')->label('Facebook'),
                                    TextInput::make('instagram_url')->label('Instagram'),
                                    TextInput::make('youtube_url')->label('YouTube channel'),
                                    TextInput::make('profile_video_url')
                                        ->label('Video profil (YouTube URL)')
                                        ->helperText('Tempel link YouTube — bisa diganti kapan saja')
                                        ->columnSpanFull(),
                                    Textarea::make('footer_text')
                                        ->label('Teks footer')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ])
                        ->columnSpanFull(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan pengaturan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $record = $this->getRecord();
        $record->fill($data);
        $record->save();

        Notification::make()
            ->success()
            ->title('Pengaturan disimpan')
            ->send();
    }

    public function getRecord(): SiteSetting
    {
        return SiteSetting::current();
    }
}
