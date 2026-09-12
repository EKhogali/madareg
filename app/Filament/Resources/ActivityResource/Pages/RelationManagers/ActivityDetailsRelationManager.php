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

    // public function form(Form $form): Form
    // {
    //     return $form->schema([
    //         Select::make('subscriber_id')
    //             ->label('المشترك')
    //             ->relationship(
    //                 name: 'subscriber',
    //                 titleAttribute: 'name',
    //                 modifyQueryUsing: function ($query) {
    //                     $user = auth()->user();
    //                     // Supervisor: only subscribers in their groups
    //                     if ($user?->isSupervisor()) {
    //                         $groupIds = $user->groups()->pluck('groups.id');
    //                         $query->whereIn('group_id', $groupIds);
    //                     }
    //                     return $query;
    //                 }
    //             )
    //             ->preload()
    //             ->searchable()
    //             ->required(),

    //         Checkbox::make('att_present')
    //             ->label('حضور (5 درجات)')
    //             ->live()
    //             ->disabled(fn (Forms\Get $get) => (bool) $get('att_on_time'))
    //             ->dehydrated(),

    //         Checkbox::make('att_on_time')
    //             ->label('حضور في الموعد (5 درجات، ويشمل الحضور)')
    //             ->live()
    //             ->afterStateUpdated(function (Forms\Set $set, $state) {
    //                 if ($state) {
    //                     $set('att_present', true);
    //                 }
    //             }),

    //         Checkbox::make('att_interaction')
    //             ->label('تفاعل (5 درجات)')
    //             ->live(),

    //         Checkbox::make('att_subscription')
    //             ->label('اشتراك (5 درجات)')
    //             ->live(),

    //         Placeholder::make('evaluation_preview')
    //             ->label('إجمالي الدرجات')
    //             ->content(function (Forms\Get $get) {
    //                 $total = ($get('att_present') ? 5 : 0)
    //                     + ($get('att_on_time') ? 5 : 0)
    //                     + ($get('att_interaction') ? 5 : 0)
    //                     + ($get('att_subscription') ? 5 : 0);
    //                 return "{$total} / 25";
    //             }),

    //         Textarea::make('notes')->rows(2)->label('ملاحظات'),
    //     ]);
    // }

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

protected function mutateFormDataBeforeFill(array $data): array
{
    $eval = (int) ($data['evaluation'] ?? 0);

    // Reverse-engineer: interactive (5) + subscribed (5) + attendance (5 or 10)
    $interactive = false;
    $subscribed  = false;
    $attendance  = 0;

    if ($eval >= 5) {
        // Try to detect interactive
        if ($eval % 5 === 0) {
            $remaining = $eval;
            if ($remaining >= 5) { $interactive = true; $remaining -= 5; }
            if ($remaining >= 5) { $subscribed  = true; $remaining -= 5; }
            $attendance = $remaining; // 0, 5, or 10
        } else {
            $attendance = $eval; // fallback
        }
    }

    $data['attendance']   = $attendance;
    $data['subscribed']   = $subscribed;
    $data['interactive']  = $interactive;

    return $data;
}

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

        // ── Attendance: single radio choice ──
        \Filament\Forms\Components\Radio::make('attendance')
            ->label('الحضور')
            ->options([
                0  => 'غائب (0 درجات)',
                5  => 'حضور (5 درجات)',
                10 => 'حضور في الموعد (10 درجات)',
            ])
            ->default(0)
            ->inline()
            ->live()
            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                $set('evaluation', $this->calcEvaluation(
                    (int) $state,
                    (bool) $get('subscribed'),
                    (bool) $get('interactive'),
                ));
            })
            ->dehydrated(false), // virtual field, not saved to DB

        \Filament\Forms\Components\Grid::make(2)->schema([

            \Filament\Forms\Components\Toggle::make('subscribed')
                ->label('اشتراك (5 درجات)')
                ->default(false)
                ->live()
                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                    $set('evaluation', $this->calcEvaluation(
                        (int) $get('attendance'),
                        (bool) $state,
                        (bool) $get('interactive'),
                    ));
                })
                ->dehydrated(false),

            \Filament\Forms\Components\Toggle::make('interactive')
                ->label('تفاعل (5 درجات)')
                ->default(false)
                ->live()
                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                    $set('evaluation', $this->calcEvaluation(
                        (int) $get('attendance'),
                        (bool) $get('subscribed'),
                        (bool) $state,
                    ));
                })
                ->dehydrated(false),
        ]),

        // ── Total (read-only display + actual saved field) ──
        \Filament\Forms\Components\Placeholder::make('evaluation_display')
            ->label('إجمالي الدرجات')
            ->content(fn(\Filament\Forms\Get $get): string =>
                ($get('evaluation') ?? 0) . ' / 20'
            ),

        \Filament\Forms\Components\Hidden::make('evaluation')
            ->default(0),

        \Filament\Forms\Components\Textarea::make('notes')
            ->rows(2)
            ->label('ملاحظات'),
    ]);
}

// ── Helper: sum points from choices ──
private function calcEvaluation(int $attendance, bool $subscribed, bool $interactive): int
{
    return $attendance
        + ($subscribed  ? 5 : 0)
        + ($interactive ? 5 : 0);
}

}
