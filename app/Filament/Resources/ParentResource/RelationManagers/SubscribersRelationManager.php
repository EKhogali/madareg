<?php

namespace App\Filament\Resources\ParentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Group;
use App\Models\Stage;
use App\Models\Subscriber;

class SubscribersRelationManager extends RelationManager
{
    protected static string $relationship = 'subscribers';
    protected static ?string $title = 'الأبناء / المشتركون';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('اسم المشترك')->required(),

            Forms\Components\Select::make('stage_id')
                ->label('المرحلة')
                ->options(Stage::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\Select::make('group_id')
                ->label('المجموعة')
                ->options(function () {
                    $u = auth()->user();

                    if ($u?->isSupervisor()) {
                        return $u->groups()->orderBy('name')->pluck('name', 'groups.id')->toArray();
                    }

                    return Group::query()->orderBy('name')->pluck('name', 'id')->toArray();
                })
                ->searchable()
                ->required(),


            Forms\Components\Select::make('gender')
                ->label('الجنس')
                ->options([
                    'male' => 'ذكر',
                    'female' => 'أنثى',
                ])
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('المشترك'),
            Tables\Columns\TextColumn::make('group.name')->label('المجموعة'),
            Tables\Columns\TextColumn::make('stage.name')->label('المرحلة'),
        ])->headerActions([
                    Tables\Actions\CreateAction::make(),
                ])->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]);
    }
}
