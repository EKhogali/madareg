<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Support\Traits\HasLauncherBackAction;

class ListActivities extends ListRecords
{
    use HasLauncherBackAction;
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            Actions\CreateAction::make(),
        ];
    }
}
