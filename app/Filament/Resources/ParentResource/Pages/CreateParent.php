<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Support\Traits\HasLauncherBackAction;

use Filament\Actions\Action;



class CreateParent extends CreateRecord
{
    use HasLauncherBackAction;

    protected static string $resource = ParentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = User::ROLE_PARENT;
        $data['status'] = (int) ($data['status'] ?? 1);
        return $data;
    }

    
    protected function getHeaderActions(): array
{
    return [
        $this->getLauncherBackAction(),
        // keep existing actions:
        \Filament\Actions\CreateAction::make(),
    ];
}

}
