<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->requiresConfirmation()
            ->modalHeading('حذف النشاط')
            ->modalDescription('هذا الإجراء سيحذف النشاط وجميع تفاصيله.')
            ->modalSubmitActionLabel('حذف')
            ->form([
                \Filament\Forms\Components\TextInput::make('confirmation')
                    ->label('اكتب DELETE للتأكيد')
                    ->required()
                    ->rules(['in:DELETE']),
            ]),
            // Actions\DeleteAction::make(),
        ];
    }

    public static function canDelete($record): bool
{
    return auth()->user()?->canManageActivities() ?? false;
}

    
}
