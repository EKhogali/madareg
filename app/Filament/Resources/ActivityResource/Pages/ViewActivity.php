<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use App\Models\ActivityDetail;
use App\Models\SupervisorActivityDetail;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            // ✅ Add Subscriber Button
            Actions\Action::make('addSubscriber')
                ->label('إضافة مشترك للنشاط')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->modalHeading('إضافة مشترك للنشاط')
                ->form([
                    \Filament\Forms\Components\Select::make('subscriber_id')
                        ->label('المشترك')
                        ->relationship('details.subscriber', 'name')
                        ->searchable()
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('evaluation')
                        ->label('التقييم')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    ActivityDetail::create([
                        'activity_id' => $this->record->id,
                        'subscriber_id' => $data['subscriber_id'],
                        'evaluation' => $data['evaluation'],
                        'notes' => $data['notes'] ?? null,
                    ]);
                })
                ->successNotificationTitle('تمت إضافة المشترك ✅'),

            // ✅ Add Supervisor Button
            Actions\Action::make('addSupervisor')
                ->label('إضافة مشرف للنشاط')
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->modalHeading('إضافة مشرف للنشاط')
                ->form([
                    \Filament\Forms\Components\Select::make('supervisor_id')
                        ->label('المشرف')
                        ->relationship('supervisorActivityDetails.supervisor', 'name')
                        ->searchable()
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    SupervisorActivityDetail::create([
                        'activity_id' => $this->record->id,
                        'supervisor_id' => $data['supervisor_id'],
                        'notes' => $data['notes'] ?? null,
                    ]);
                })
                ->successNotificationTitle('تمت إضافة المشرف ✅'),
        ];
    }
}
