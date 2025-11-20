<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodicEvaluationResource\Pages;
use App\Filament\Resources\PeriodicEvaluationResource\RelationManagers;
use App\Models\PeriodicEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Carbon;

class PeriodicEvaluationResource extends Resource
{
    protected static ?string $model = PeriodicEvaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'التقييمات الدورية';
    protected static ?string $modelLabel = 'تقييم دوري';
    protected static ?string $pluralModelLabel = 'التقييمات الدورية';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('title')->required()->label('عنوان التقييم'),
            DatePicker::make('from_date')->required()->label('من تاريخ')->default(Carbon::now()),
            DatePicker::make('to_date')->required()->label('إلى تاريخ')->default(Carbon::now()),
            Textarea::make('description')->label('الوصف')->rows(4),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->sortable()->searchable(),
                TextColumn::make('from_date')->label('من تاريخ'),
                TextColumn::make('to_date')->label('إلى تاريخ'),
                IconColumn::make('is_closed')
                    ->label('مغلق')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PeriodicEvaluationDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriodicEvaluations::route('/'),
            'create' => Pages\CreatePeriodicEvaluation::route('/create'),
            'edit' => Pages\EditPeriodicEvaluation::route('/{record}/edit'),
            // 'view' => Pages\ViewPeriodicEvaluation::route('/{record}/view'),
        ];
    }
}
