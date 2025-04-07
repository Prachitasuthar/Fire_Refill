<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, $roleOrPermission)
    {
        if (!Auth::check() || (!Auth::user()->hasRole($roleOrPermission) && !Auth::user()->hasPermissionTo($roleOrPermission))) {
            throw UnauthorizedException::forRolesOrPermissions([$roleOrPermission]);
        }
        return $next($request);
    }
}
