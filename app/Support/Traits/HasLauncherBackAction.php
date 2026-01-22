<?php

// namespace App\Filament\Support;
namespace App\Support\Traits;

use Filament\Actions\Action;

trait HasLauncherBackAction
{
    protected function getLauncherBackAction(): Action
    {
        return Action::make('backToLauncher')
            ->label('العودة للتطبيقات')
            ->icon('heroicon-o-squares-2x2')
            ->url(\App\Filament\Pages\AppLauncher::getUrl())
            ->color('gray');
    }
}
