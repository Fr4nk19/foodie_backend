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

        // If company_admin, enforce that the route company matches the user's company_id.
        // super_admin can access any company.
        if ($user->isCompanyAdmin()) {
            $routeCompany = $request->route('company'); // can be ID or model (route model binding)
            $routeCompanyId = is_object($routeCompany) ? ($routeCompany->id ?? null) : $routeCompany;

            if ($routeCompanyId && (int) $routeCompanyId !== (int) $user->company_id) {
                return response()->json([
                    'message' => 'Forbidden. You do not have access to this company.',
                ], 403);
            }
        }

        return $next($request);
    }
}
