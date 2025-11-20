<?php

namespace App\Filament\Resources\PeriodicEvaluationDetailResource\Pages;

use App\Filament\Resources\PeriodicEvaluationDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodicEvaluationDetails extends ListRecords
{
    protected static string $resource = PeriodicEvaluationDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
