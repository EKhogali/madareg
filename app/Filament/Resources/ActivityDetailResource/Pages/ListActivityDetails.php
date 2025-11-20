<?php

namespace App\Filament\Resources\ActivityDetailResource\Pages;

use App\Filament\Resources\ActivityDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityDetails extends ListRecords
{
    protected static string $resource = ActivityDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
