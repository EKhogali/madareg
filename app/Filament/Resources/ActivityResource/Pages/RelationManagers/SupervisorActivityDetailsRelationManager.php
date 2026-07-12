<?php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class SupervisorActivityDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisorActivityDetails';
    protected static ?string $title = 'مشرفي النشاط';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->canManageActivities() ?? false;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([

            Forms\Components\Select::make('supervisor_id')
                ->label('المشرف')
                ->options(
                    User::whereIn('role', [2, 3])
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->required(),

            Forms\Components\Select::make('activity_role')
                ->label('الدور')
                ->options([
                    1 => 'مشرف عام',
                    2 => 'مشرف',
                ])
                ->default(2)
                ->required(),

            Forms\Components\TextInput::make('evaluation')
                ->label('التقييم')
                ->numeric()
                ->minValue(1)
                ->maxValue(30)
                ->default(1)
                ->required()
                ->visible(fn() => auth()->user()?->isSuperAdmin()),

            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات')
                ->rows(2)
                ->nullable(),

        ]);
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('المشرف')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('activity_role')
                    ->label('الدور')
                    ->formatStateUsing(fn($state) => match((int)$state) {
                        1 => 'مشرف عام',
                        2 => 'مشرف',
                        default => '—',
                    })
                    ->colors([
                        'warning' => fn($state) => (int)$state === 1,
                        'primary' => fn($state) => (int)$state === 2,
                    ]),

                Tables\Columns\TextColumn::make('evaluation')
                    ->label('التقييم')
                    ->sortable()
                    ->visible(fn() => auth()->user()?->isSuperAdmin()),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->wrap()
                    ->limit(60),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة مشرف')
                    ->visible(fn() => auth()->user()?->isSuperAdmin()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->isSuperAdmin()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->isSuperAdmin()),
            ])
            ->defaultSort('id', 'desc');
    }
}