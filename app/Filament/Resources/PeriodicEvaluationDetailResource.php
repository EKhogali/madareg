<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodicEvaluationDetailResource\Pages;
use App\Filament\Resources\PeriodicEvaluationDetailResource\RelationManagers;
use App\Models\PeriodicEvaluationDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PeriodicEvaluationDetailResource extends Resource
{
    protected static ?string $model = PeriodicEvaluationDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'تفاصيل التقييم';
    protected static ?string $modelLabel = 'تفصيل تقييم';
    protected static ?string $pluralModelLabel = 'تفاصيل التقييم';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('periodic_evaluation_id')->relationship('periode', 'title')->label('التقييم الدوري')->required(),
            Select::make('subscriber_id')->relationship('subscriber', 'name')->label('المشترك')->required(),
            // TextInput::make('evaluation_area')->numeric()->label('مجال التقييم')->required(),
                Select::make('evaluation_area')
                    ->label('مجال التقييم')
                    ->options([
                        'المُبادرة' => 'المُبادرة',
                        'التفاعل' => 'التفاعل',
                        'الالتزام' => 'الالتزام',
                        // 'أخرى' => 'أخرى',
                    ])
                    ->required(),
            TextInput::make('evaluation_score')->numeric()->minValue(1)->maxValue(10)->label('درجة التقييم')->required(),
            Textarea::make('notes')->rows(3)->label('ملاحظات'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('periode.title')->label('التقييم الدوري'),
                Tables\Columns\TextColumn::make('subscriber.name')->label('المشترك'),
                Tables\Columns\TextColumn::make('evaluation_area')->label('مجال التقييم'),
                Tables\Columns\TextColumn::make('evaluation_score')->label('درجة التقييم'),
                Tables\Columns\TextColumn::make('notes')->label('ملاحظات')->limit(30),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListPeriodicEvaluationDetails::route('/'),
            'create' => Pages\CreatePeriodicEvaluationDetail::route('/create'),
            'edit' => Pages\EditPeriodicEvaluationDetail::route('/{record}/edit'),
        ];
    }
}
