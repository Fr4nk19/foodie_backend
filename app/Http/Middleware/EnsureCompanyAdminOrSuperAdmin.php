<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isSuperAdmin() && ! $user->isCompanyAdmin())) {
            return response()->json([
                'message' => 'Forbidden. Company admin or super admin access required.',
            ], 403);
        }

        return $next($request);
    }
}
