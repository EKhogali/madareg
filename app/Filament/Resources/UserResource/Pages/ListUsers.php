<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Support\Traits\HasLauncherBackAction;

class ListUsers extends ListRecords
{
    use HasLauncherBackAction;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            Actions\CreateAction::make(),
        ];
    }
}
