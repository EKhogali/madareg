<?php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivityDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subscriber_id')
                ->relationship('subscriber', 'name')->label('المشترك')
                ->required(),
            TextInput::make('evaluation')->label('التقييم')
                ->numeric()->minValue(1)->maxValue(10)->required(),
            Textarea::make('notes')->rows(2)->label('ملاحظات'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subscriber.name')->label('المشترك'),
            TextColumn::make('evaluation')->label('التقييم'),
            TextColumn::make('notes')->wrap()->label('ملاحظات'),
        ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // add button
            ])
            ->actions([
                Tables\Actions\EditAction::make(),    // edit button
                Tables\Actions\DeleteAction::make(),  // delete button
            ])
            ->defaultSort('id', 'desc');
    }
}
