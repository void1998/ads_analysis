<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    protected function authenticate($request, array $guards)
    {

        if (! $this->auth->check()) {
            throw new AuthenticationException('Unauthenticated.', $guards, $this->redirectTo($request));
        }
    }
}
