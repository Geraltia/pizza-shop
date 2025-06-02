<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_admin) {
            return response()->json(['message' => 'Доступ запрещён. Админ только.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
