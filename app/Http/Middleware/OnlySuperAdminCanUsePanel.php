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
    $path = '/' . ltrim($request->path(), '/'); // e.g. /admin/...

    // Let Filament handle guests (login page etc.)
    if (! $user) {
        return $next($request);
    }

    // Super admin can access everything
    if ((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
        return $next($request);
    }

    // ALWAYS allow Filament internal endpoints needed for the panel to function
    if (
        str_starts_with($path, '/admin/livewire') ||
        str_starts_with($path, '/admin/filament') ||
        str_starts_with($path, '/admin/logout')
    ) {
        return $next($request);
    }

    // Allow ONLY the launcher + profile pages for non-superadmins
    $allowedPrefixes = [
        '/admin/app-launcher',
        '/admin/my-profile',
        '/admin/change-password',
    ];

    foreach ($allowedPrefixes as $prefix) {
        if (str_starts_with($path, $prefix)) {
            return $next($request);
        }
    }

    // Redirect everything else to launcher
    return redirect('/admin/app-launcher');
}

}
