<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware sprawdzający czy użytkownik jest administratorem.
 * Middleware that checks if user is an administrator.
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     * Sprawdza czy użytkownik jest zalogowany i ma uprawnienia administratora.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sprawdź czy użytkownik jest zalogowany i jest adminem
        if (!$request->user() || !$request->user()->is_admin) {
            abort(403, __('auth.unauthorized_admin'));
        }

        return $next($request);
    }
}
