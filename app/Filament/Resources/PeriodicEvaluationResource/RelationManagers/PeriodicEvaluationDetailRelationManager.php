<?php
namespace App\Filament\Resources\PeriodicEvaluationResource\RelationManagers;

use App\Models\Subscriber;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PeriodicEvaluationDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details'; // matches `details()` method in PeriodicEvaluation model

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('subscriber_id')
                ->label('العضو')
                ->relationship('subscriber', 'name')
                ->required(),

            // Forms\Components\TextInput::make('evaluation_area')
            //     ->label('مجال التقييم')
            //     ->numeric()
            //     ->required(),
            Forms\Components\Select::make('evaluation_area')
                ->label('مجال التقييم')
                ->options([
                    1 => 'المُبادرة',
                    2 => 'التفاعل',
                    3 => 'الالتزام',
                    // 'أخرى' => 'أخرى',
                ])
                ->required(),

            Forms\Components\TextInput::make('evaluation_score')
                ->label('درجة التقييم')
                ->numeric()
                ->minValue(1)
                ->maxValue(10)
                ->required(),

            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات')
                ->rows(3),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscriber.name')->label('العضو'),
                Tables\Columns\TextColumn::make('evaluation_area')->label('المجال')
                    ->formatStateUsing(fn($state) => match ($state) {
                        1 => 'المُبادرة',
                        2 => 'التفاعل',
                        3 => 'الالتزام',
                        default => 'غير معروف',
                    }),
                Tables\Columns\TextColumn::make('evaluation_score')
                    ->label('الدرجة'),
                Tables\Columns\TextColumn::make('notes')->label('ملاحظات')->limit(30),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
