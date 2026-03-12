<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $maintenance = $this->settingService->getGroup('maintenance');

        if (empty($maintenance['enabled']) || $maintenance['enabled'] === '0') {
            return $next($request);
        }

        // Admin users can always access the site
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return $next($request);
            }
        }

        // Allow whitelisted IPs
        if (!empty($maintenance['allowed_ips'])) {
            $allowedIps = array_map('trim', explode(',', $maintenance['allowed_ips']));
            if (in_array($request->ip(), $allowedIps, true)) {
                return $next($request);
            }
        }

        // Allow login route so admins can authenticate
        if ($request->routeIs('login') || $request->routeIs('login.post')) {
            return $next($request);
        }

        $message = $maintenance['message'] ?? null;

        abort(503, $message ?: 'Site bakım modundadır.');
    }
}
