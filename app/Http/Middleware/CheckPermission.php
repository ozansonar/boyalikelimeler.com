<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasAnyPermission(...$permissions)) {
            abort(403, 'Bu işlem için yetkiniz bulunmuyor.');
        }

        return $next($request);
    }
}
