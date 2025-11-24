<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityDetailResource\Pages;
use App\Filament\Resources\ActivityDetailResource\RelationManagers;
use App\Models\ActivityDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\Activity;
use App\Models\Subscriber;
use Filament\Tables\Columns\TextColumn;

class ActivityDetailResource extends Resource
{
    protected static ?string $model = ActivityDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('activity_id')
                    ->label('Activity')
                    ->relationship('activity', 'title')
                    ->required(),

                Select::make('subscriber_id')
                    ->label('Subscriber')
                    ->relationship('subscriber', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('evaluation')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->required(),

                Textarea::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity.category')->label('Activity'),
                TextColumn::make('subscriber.name')->label('Subscriber'),
                TextColumn::make('evaluation'),
                TextColumn::make('notes')->limit(30),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityDetails::route('/'),
            'create' => Pages\CreateActivityDetail::route('/create'),
            'edit' => Pages\EditActivityDetail::route('/{record}/edit'),
        ];
    }

    
}
