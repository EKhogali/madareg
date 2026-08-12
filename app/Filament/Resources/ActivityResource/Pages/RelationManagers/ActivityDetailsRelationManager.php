<?php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
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

            Checkbox::make('att_present')
                ->label('حضور (5 درجات)')
                ->live()
                ->disabled(fn (Forms\Get $get) => (bool) $get('att_on_time'))
                ->dehydrated(),

            Checkbox::make('att_on_time')
                ->label('حضور في الموعد (5 درجات، ويشمل الحضور)')
                ->live()
                ->afterStateUpdated(function (Forms\Set $set, $state) {
                    if ($state) {
                        $set('att_present', true);
                    }
                }),

            Checkbox::make('att_interaction')
                ->label('تفاعل (5 درجات)')
                ->live(),

            Checkbox::make('att_subscription')
                ->label('اشتراك (5 درجات)')
                ->live(),

            Placeholder::make('evaluation_preview')
                ->label('إجمالي الدرجات')
                ->content(function (Forms\Get $get) {
                    $total = ($get('att_present') ? 5 : 0)
                        + ($get('att_on_time') ? 5 : 0)
                        + ($get('att_interaction') ? 5 : 0)
                        + ($get('att_subscription') ? 5 : 0);
                    return "{$total} / 25";
                }),

            Textarea::make('notes')->rows(2)->label('ملاحظات'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subscriber.name')->label('المشترك'),
            IconColumn::make('att_present')->label('حضور')->boolean(),
            IconColumn::make('att_on_time')->label('حضور في الموعد')->boolean(),
            IconColumn::make('att_interaction')->label('تفاعل')->boolean(),
            IconColumn::make('att_subscription')->label('اشتراك')->boolean(),
            TextColumn::make('evaluation')->label('الإجمالي')->sortable(),
            TextColumn::make('notes')->wrap()->label('ملاحظات'),
        ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->canManageActivities() ?? false;
    }
}
