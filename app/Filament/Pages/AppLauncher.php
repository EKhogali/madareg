<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AppLauncher extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string $view = 'filament.pages.app-launcher';
    protected static ?string $navigationLabel = 'التطبيقات';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'app-launcher';



    public function getTitle(): string
    {
        return 'التطبيقات';
    }

    // ✅ Sidebar/menu item appears ONLY for user_id 1/2
    public static function shouldRegisterNavigation(): bool
    {
        $id = Auth::id();
        return in_array($id, [1, 2], true);
    }


    public static function canAccess(): bool
{
    return auth()->check(); // any authenticated user
}

    // ✅ Page access ONLY for user_id 1/2
    // public static function canAccess(): bool
    // {
    //     $id = Auth::id();
    //     return in_array($id, [1, 2], true);
    // }
}
