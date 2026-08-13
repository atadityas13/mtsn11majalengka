<?php

namespace App\Filament\Resources\StaffMembers;

use App\Filament\Concerns\RequiresSuperAdmin;
use App\Filament\Resources\StaffMembers\Pages\CreateStaffMember;
use App\Filament\Resources\StaffMembers\Pages\EditStaffMember;
use App\Filament\Resources\StaffMembers\Pages\ListStaffMembers;
use App\Filament\Resources\StaffMembers\Schemas\StaffMemberForm;
use App\Filament\Resources\StaffMembers\Tables\StaffMembersTable;
use App\Models\StaffMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StaffMemberResource extends Resource
{
    use RequiresSuperAdmin;

    protected static ?string $model = StaffMember::class;

    protected static ?string $navigationLabel = 'Guru & Tendik';

    protected static ?string $modelLabel = 'Guru & Tendik';

    protected static ?string $pluralModelLabel = 'Guru & Tendik';

    protected static string|UnitEnum|null $navigationGroup = 'Profil';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return StaffMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffMembers::route('/'),
            'create' => CreateStaffMember::route('/create'),
            'edit' => EditStaffMember::route('/{record}/edit'),
        ];
    }
}
