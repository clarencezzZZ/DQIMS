<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // If user is admin, allow access to all routes except specific restricted routes for adminfront
        if ($user->isAdmin()) {
            // Check if this is the adminfront user and the route is restricted
            if ($user->username === 'adminfront' && $this->isAdminRestrictedRoute($request)) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }

    /**
     * Check if the current route is restricted for adminfront user
     */
    private function isAdminRestrictedRoute(Request $request): bool
    {
        $currentRoute = $request->route()->getName();
        
        // Define routes that are restricted for adminfront user
        $restrictedRoutes = [
            'admin.users',
            'admin.users.store',
            'admin.users.update',
        ];

        foreach ($restrictedRoutes as $route) {
            if ($currentRoute && str_starts_with($currentRoute, $route)) {
                return true;
            }
        }

        // Also check for routes by URI
        $requestUri = $request->getRequestUri();
        if (str_contains($requestUri, '/admin/users') || str_contains($requestUri, '/admin/categories')) {
            return true;
        }

        return false;
    }
}
