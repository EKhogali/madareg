<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Support\Traits\HasLauncherBackAction;

class ListParents extends ListRecords
{
    use HasLauncherBackAction;
    protected static string $resource = ParentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->isSuperAdmin() || auth()->user()?->isSupervisor()),
        ];
    }
}
