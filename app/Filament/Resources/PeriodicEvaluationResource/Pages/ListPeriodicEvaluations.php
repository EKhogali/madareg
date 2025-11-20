<?php

namespace App\Filament\Resources\PeriodicEvaluationResource\Pages;

use App\Filament\Resources\PeriodicEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodicEvaluations extends ListRecords
{
    protected static string $resource = PeriodicEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
