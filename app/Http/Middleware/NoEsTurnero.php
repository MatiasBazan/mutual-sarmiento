<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoEsTurnero
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isTurnero()) {
            abort(403, 'Acceso no disponible para este rol.');
        }

        return $next($request);
    }
}
