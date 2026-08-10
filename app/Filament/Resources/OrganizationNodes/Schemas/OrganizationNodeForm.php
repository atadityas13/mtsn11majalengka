<?php

namespace App\Filament\Resources\OrganizationNodes\Schemas;

use App\Models\OrganizationNode;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrganizationNodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                        if (blank($get('slug')) && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('name')
                    ->label('Nama pejabat')
                    ->maxLength(255)
                    ->helperText('Boleh dikosongkan dulu — diisi setelah SK keluar'),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('parent_id')
                    ->label('Atasan langsung')
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn ($query) => $query->orderBy('sort_order'),
                    )
                    ->getOptionLabelFromRecordUsing(fn (OrganizationNode $record): string => $record->title.($record->name ? ' — '.$record->name : ''))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('lane')
                    ->label('Posisi di bagan')
                    ->options([
                        'peer_top' => 'Sejajar puncak (Komite / Kamad)',
                        'line' => 'Garis komando',
                        'staff' => 'Staf / unit pendukung',
                        'collective' => 'Node kolektif (Guru)',
                    ])
                    ->required()
                    ->default('line'),
                FileUpload::make('photo')
                    ->label('Foto (bisa diganti)')
                    ->image()
                    ->directory('organization')
                    ->disk('public')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Tupoksi singkat')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
