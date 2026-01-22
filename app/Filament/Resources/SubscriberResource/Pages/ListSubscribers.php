<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Support\Traits\HasLauncherBackAction;


class ListSubscribers extends ListRecords
{
    use HasLauncherBackAction;
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getLauncherBackAction(),
            Actions\CreateAction::make(),
        ];
    }
}
