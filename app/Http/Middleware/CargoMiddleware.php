<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CargoMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$cargos
    ): Response {

        if (!auth()->check()) {
            abort(401);
        }

        if (!in_array(auth()->user()->cargo, $cargos)) {
            abort(403);
        }

        return $next($request);
    }
}