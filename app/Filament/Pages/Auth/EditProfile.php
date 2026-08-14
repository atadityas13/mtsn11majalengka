<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return 'Profil saya';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Profil saya';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->description('Data yang tampil di panel Si COMA.')
                    ->schema([
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getUsernameFormComponent(),
                        $this->getEmailFormComponent(),
                    ])
                    ->columns(1),
                Section::make('Kata sandi')
                    ->description('Kosongkan jika tidak ingin mengganti kata sandi.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Foto profil')
            ->image()
            ->avatar()
            ->imageEditor()
            ->circleCropper()
            ->disk('public')
            ->directory('avatars')
            ->visibility('public')
            ->fetchFileInformation(false)
            ->maxSize(2048)
            ->helperText('JPG, PNG, atau WEBP. Maksimal 2 MB.');
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->unique(ignoreRecord: true)
            ->rule('regex:/^[a-zA-Z0-9._-]+$/')
            ->helperText('Dipakai untuk masuk ke Si COMA.')
            ->maxLength(50)
            ->autocomplete('username');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->live(debounce: 500);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata sandi baru')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrated(fn (#[SensitiveParameter] $state): bool => filled($state))
            ->dehydrateStateUsing(fn (#[SensitiveParameter] $state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Ulangi kata sandi baru')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label('Kata sandi saat ini')
            ->helperText('Wajib diisi jika mengganti email atau kata sandi.')
            ->password()
            ->autocomplete('current-password')
            ->currentPassword(guard: \Filament\Facades\Filament::getAuthGuard())
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
            ->dehydrated(false);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Profil berhasil disimpan';
    }
}
