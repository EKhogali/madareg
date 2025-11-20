<?php

namespace App\Filament\Resources\ActivityDetailResource\Pages;

use App\Filament\Resources\ActivityDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityDetail extends EditRecord
{
    protected static string $resource = ActivityDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
