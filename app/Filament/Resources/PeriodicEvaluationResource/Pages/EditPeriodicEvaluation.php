<?php

namespace App\Filament\Resources\PeriodicEvaluationResource\Pages;

use App\Filament\Resources\PeriodicEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodicEvaluation extends EditRecord
{
    protected static string $resource = PeriodicEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
