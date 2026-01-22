<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Support\Traits\HasLauncherBackAction;

class ListGroups extends ListRecords
{
    use HasLauncherBackAction;
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            Actions\CreateAction::make(),
        ];
    }
}
