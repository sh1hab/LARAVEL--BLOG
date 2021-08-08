<?php

namespace App\Http\Middleware;

use App\Http\Traits\RespondTrait;
use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    use RespondTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = $this->getRequiredRoleForRoute($request->route());

        if (!$roles || $request->user()->hasRole($roles)) {
            return $next($request);
        }
        return $this->respondForbidden(__('app.user.role.forbidden'), __('app.status.forbidden'));
    }

    private function getRequiredRoleForRoute($route)
    {
        $actions = $route->getAction();
        return $actions['roles'] ?? null;
    }
}
