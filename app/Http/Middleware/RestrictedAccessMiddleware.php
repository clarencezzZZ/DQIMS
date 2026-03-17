<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Auth;

class RestrictedAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip check for main admin
        if (Auth::check() && Auth::user()->username === 'admin') {
            return $next($request);
        }

        $module = ModuleSetting::where('module_key', 'restricted_access')->first();
        
        if ($module && !$module->is_enabled) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module is currently locked. Please contact the admin to enable access.'
                ], 403);
            }

            return response()->view('errors.restricted', [
                'message' => 'Unlock please contact the admin to enable this'
            ], 403);
        }

        return $next($request);
    }
}
