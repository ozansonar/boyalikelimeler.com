<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
