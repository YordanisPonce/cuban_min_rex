<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DevMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role=='developer') {
            $dashboardUrl = '/admin/dev-dashboard';
            
            if ($request->path() === 'admin' || $request->path() === 'admin/') {
                return redirect($dashboardUrl);
            }

            if (!str_contains($request->path(), 'dev-dashboard')) {

                $allowedPaths = ['admin/logout', 'admin/profile'];
                $isAllowed = false;

                foreach ($allowedPaths as $path) {
                    if (str_contains($request->path(), $path)) {
                        $isAllowed = true;
                        break;
                    }
                }

                if (!$isAllowed && $request->path() !== 'admin/dev-dashboard' && $request->path() !== 'admin/dev-radio-earnings') {
                    return redirect($dashboardUrl);
                }
            }
        }

        return $next($request);
    }
}
