<?php

namespace App\Filament\Resources;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\GroupMembersRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\GroupSupervisorsRelationManager;

use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'المجموعات';
    protected static ?string $modelLabel = 'مجموعة';
    protected static ?string $pluralModelLabel = 'المجموعات';

    protected static ?string $navigationGroup = 'البيانات الأساسية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Group Info'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label(__('Name')),

                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3),

                        DatePicker::make('date_range_start')
                            ->required()
                            ->label(__('Start Date')),

                        DatePicker::make('date_range_end')
                            ->required()
                            ->label(__('End Date')),

                        Toggle::make('active')
                            ->label(__('Active'))
                            ->default(true),

                        ColorPicker::make('color')
                            ->label(__('Color'))
                            ->default('#FFFFFF'),
                    ])
                    ->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date_range_start')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('date_range_end')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable(),

                BadgeColumn::make('active')
                    ->label(__('Active'))
                    ->colors([
                        'success' => fn($state): bool => $state,
                        'danger' => fn($state): bool => !$state,
                    ])
                    ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No')),

                TextColumn::make('color')
                    ->label(__('Color')),
            ])
            ->filters([
                SelectFilter::make('active')
                    ->label(__('Active'))
                    ->options([
                        1 => __('Yes'),
                        0 => __('No'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            GroupSupervisorsRelationManager::class,
            GroupMembersRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 3) {
            $query->whereHas('users', function ($q) {
                $q->where('users.id', auth()->id());
            });
        }

        return $query;
    }

}
