<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentResource\Pages;
use App\Filament\Resources\ParentResource\RelationManagers\SubscribersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'أولياء الأمور';
    protected static ?string $modelLabel = 'ولي أمر';
    protected static ?string $pluralModelLabel = 'أولياء الأمور';
    protected static ?string $navigationGroup = 'البيانات الأساسية';

    public static function shouldRegisterNavigation(): bool
{
    $user = auth()->user();

    return $user?->isSuperAdmin() || $user?->isSupervisor();
}

public static function canViewAny(): bool
{
    $user = auth()->user();
    return $user?->isSuperAdmin() || $user?->isSupervisor();
}

public static function canCreate(): bool
{
    $user = auth()->user();
    return $user?->isSuperAdmin() || $user?->isSupervisor();
}

public static function canEdit($record): bool
{
    $user = auth()->user();

    // Allow Super Admin always
    if ($user?->isSuperAdmin()) {
        return true;
    }

    // Supervisor: only if this parent has subscribers in supervisor's groups
    if ($user?->isSupervisor()) {
        $groupIds = $user->groups()->pluck('groups.id')->toArray();

        return $record->subscribers()->whereIn('group_id', $groupIds)->exists();
    }

    return false;
}


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->label('البريد')->email()->required()->maxLength(255),

            // Optional phone if column exists
            Forms\Components\TextInput::make('phone')->label('الهاتف')->maxLength(30),

            // Password on create only (edit handled elsewhere)
            Forms\Components\TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->revealable()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->same('password_confirmation'),

            Forms\Components\TextInput::make('password_confirmation')
                ->label('تأكيد كلمة المرور')
                ->password()
                ->revealable()
                ->dehydrated(false)
                ->required(fn (string $operation) => $operation === 'create'),

            Forms\Components\Toggle::make('status')->label('نشط')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('البريد')->searchable(),
            Tables\Columns\IconColumn::make('status')->label('نشط')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()->where('role', User::ROLE_PARENT);
    $user = auth()->user();

    if (! $user) {
        return $query->whereRaw('1=0');
    }

    // Super admin sees all parents
    if ($user->isSuperAdmin()) {
        return $query;
    }

    // Supervisor sees only parents that have subscribers in their groups
    if ($user->isSupervisor()) {
        $groupIds = $user->groups()->pluck('groups.id')->toArray();

        return $query->whereHas('subscribers', fn ($q) => $q->whereIn('group_id', $groupIds));
    }

    // others see nothing
    return $query->whereRaw('1=0');
}


    public static function getRelations(): array
    {
        return [
            SubscribersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParents::route('/'),
            'create' => Pages\CreateParent::route('/create'),
            'edit' => Pages\EditParent::route('/{record}/edit'),
        ];
    }
}
