<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class OnlySuperAdminCanUsePanel
{
    public function handle(Request $request, Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // 1. Get the current path (e.g., 'admin' or 'admin/subscribers')
        $path = $request->path();

        // Always redirect the panel root ( /admin ) to the App Launcher for ALL users
        if ($path === 'admin' || $path === 'admin/') {
            return redirect()->to('/admin/app-launcher');
        }

        // Super Admin (and user id 2) can still access everything else
        if ($user->id === 2 || (int) $user->role === 1) {
            return $next($request);
        }


        // 4. Always allow internal Filament/Livewire requests
        if (str_contains($path, 'livewire') || str_contains($path, 'filament')) {
            return $next($request);
        }

        // 5. Define allowed paths for non-admins
        $allowedPrefixes = [
            'admin/app-launcher',
            'admin/my-profile',
            'admin/change-password',
            'admin/subscribers',
            'admin/parents',
            'admin/follow-up-monthly-sheet',
            'admin/monthly-follow-up-report',
            'admin/logout',
            'admin/login',
            'admin/auth',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        // 6. If they try to access anything else, send them to the launcher
        return redirect()->to('/admin/app-launcher');
    }

}
