<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AppLauncher extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'التطبيقات';
    protected static ?string $navigationGroup = null; // keep top-level
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.app-launcher';

    public function getTitle(): string
    {
        return 'التطبيقات';
    }

    /**
     * ✅ Sidebar/menu item appears ONLY for role=3
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user && (int) $user->role === 3;
    }
}
