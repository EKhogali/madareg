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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => static::mapAttendance($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        // تحويل الأعمدة المحفوظة إلى قيمة زر الحضور
                        $data['attendance'] = ! empty($data['att_on_time']) ? 10 : (! empty($data['att_present']) ? 5 : 0);
                        return $data;
                    })
                    ->mutateFormDataUsing(fn (array $data): array => static::mapAttendance($data)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->canManageActivities() ?? false;
    }

    // ── تحويل زر الحضور (0/5/10) إلى الأعمدة المحفوظة ──
    // الموديل [ActivityDetail] هو من يحسب evaluation من هذه الأعمدة عند الحفظ
    protected static function mapAttendance(array $data): array
    {
        $attendance = (int) ($data['attendance'] ?? 0);

        $data['att_present'] = $attendance >= 5;
        $data['att_on_time'] = $attendance >= 10;

        unset($data['attendance']);

        return $data;
    }

    // ── مجموع الدرجات للعرض الحي في النموذج ──
    protected static function previewTotal(\Filament\Forms\Get $get): int
    {
        return (int) $get('attendance')
            + ($get('att_subscription') ? 5 : 0)
            + ($get('att_interaction') ? 5 : 0);
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

            // ── الحضور: اختيار واحد (حقل افتراضي يُحوَّل إلى att_present / att_on_time عند الحفظ) ──
            \Filament\Forms\Components\Radio::make('attendance')
                ->label('الحضور')
                ->options([
                    0  => 'غائب (0 درجات)',
                    5  => 'حضور (5 درجات)',
                    10 => 'حضور في الموعد (10 درجات)',
                ])
                ->default(0)
                ->inline()
                ->live(),

            \Filament\Forms\Components\Grid::make(2)->schema([
                \Filament\Forms\Components\Toggle::make('att_subscription')
                    ->label('اشتراك (5 درجات)')
                    ->default(false)
                    ->live(),

                \Filament\Forms\Components\Toggle::make('att_interaction')
                    ->label('تفاعل (5 درجات)')
                    ->default(false)
                    ->live(),
            ]),

            // ── الإجمالي (للعرض فقط، والحساب الفعلي في الموديل) ──
            \Filament\Forms\Components\Placeholder::make('evaluation_display')
                ->label('إجمالي الدرجات')
                ->content(fn (\Filament\Forms\Get $get): string => static::previewTotal($get) . ' / 20'),

            \Filament\Forms\Components\Textarea::make('notes')
                ->rows(2)
                ->label('ملاحظات'),
        ]);
    }
}
