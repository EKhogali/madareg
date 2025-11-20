<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\RelationManagers\ActivityDetailsRelationManager;


use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'الأنشطة';
    protected static ?string $modelLabel = 'نشاط';
    protected static ?string $pluralModelLabel = 'الأنشطة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->label('عنوان المنشط'),
                DatePicker::make('from_date')->required()->label('تاريخ بداية المنشط')
                    ->default(Carbon::now()),
                DatePicker::make('to_date')->required()->label('تاريخ نهاية المنشط')
                    ->default(Carbon::now()),
                Select::make('category')
                    ->label('تصنيف المنشط')
                    ->options([
                        'ترفيهي' => 'ترفيهي',
                        'رياضي' => 'رياضي',
                        'إبداعي' => 'إبداعي',
                        'أخرى' => 'أخرى',
                    ])
                    ->required(),
                Textarea::make('description')->rows(4)->label('وصف المنشط'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان المنشط')->sortable()->searchable(),
                TextColumn::make('category')->label('تصنيف المنشط'),
                TextColumn::make('from_date')->label('تاريخ بداية المنشط'),
                TextColumn::make('to_date')->label('تاريخ نهاية المنشط'),
                TextColumn::make('description')->label('وصف المنشط')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('عرض مع التفاصيل')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Activity $record): string => route('filament.admin.resources.activities.view', ['record' => $record])),
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
            ActivityDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
            'view' => Pages\ViewActivity::route('/{record}/view'),
        ];
    }
}
