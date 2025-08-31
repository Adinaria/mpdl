<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Всегда подставляю заголовок Json, так как в ответ должны быть нормальные ответы, а не 405 страница
 */
class ForceJsonResponseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
