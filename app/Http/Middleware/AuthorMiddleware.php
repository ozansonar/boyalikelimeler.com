<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isYazar() && !$user->isSuperAdmin() && !$user->isAdmin()) {
            return redirect()->route('profile.show', $user->username)
                ->with('warning', 'Bu sayfaya erişmek için yazar olmanız gerekmektedir. Profilinizden yazar başvurusu yapabilirsiniz.');
        }

        return $next($request);
    }
}
