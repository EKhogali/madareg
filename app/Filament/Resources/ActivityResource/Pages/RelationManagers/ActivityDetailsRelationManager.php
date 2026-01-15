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
use App\Models\ActivityDetail;


use Illuminate\Database\Eloquent\Model;

class ActivityDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = "المشتركون في النشاط";

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subscriber_id')
                ->label('المشترك')
                ->relationship(
                    name: 'subscriber',
                    titleAttribute: 'name',
                    modifyQueryUsing: function ($query) {
                        $user = auth()->user();

                        // Supervisor: only subscribers in their groups
                        if ($user?->isSupervisor()) {
                            $groupIds = $user->groups()->pluck('groups.id');
                            $query->whereIn('group_id', $groupIds);
                        }

                        return $query;
                    }
                )
                ->preload()
                ->searchable()
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
                    // Tables\Actions\CreateAction::make()
                    //     ->label('إضافة تقييم جديد')
                    //     ->model(\App\Models\ActivityDetail::class)
                    //     ->using(function (array $data, $record) {
                    //         $data['activity_id'] = $record->id;
                    //         return ActivityDetail::create($data);
                    //     })

                ])
            ->actions([
                    Tables\Actions\EditAction::make(),    // edit button
                    Tables\Actions\DeleteAction::make(),  // delete button
                ])
            ->defaultSort('id', 'desc');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
{
    return auth()->user()?->isStaff() ?? false;
}



    // public function canCreate(): bool
    // {
    //     return true;
    // }

    // public function canEdit(Model $record): bool
    // {
    //     return true;
    // }

    // public function canDelete(Model $record): bool
    // {
    //     return true;
    // }


}
