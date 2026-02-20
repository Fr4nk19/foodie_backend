<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\StoreCompanyRequest;
use App\Http\Resources\Api\V1\CompanyResource;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * GET /api/v1/companies
     *
     * List all companies with their default branch.
     * Requires: auth:sanctum + super_admin role.
     */
    public function index(): JsonResponse
    {
        $companies = Company::with('branches', 'defaultBranch')
            ->latest()
            ->get();

        return response()->json([
            'companies' => CompanyResource::collection($companies),
        ]);
    }

    /**
     * POST /api/v1/companies
     *
     * Create a new company and its default branch.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $slug = $this->uniqueSlug($request->name);

        $company = Company::create([
            'name'     => $request->name,
            'slug'     => $slug,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'city'     => $request->city,
            'state'    => $request->state,
            'country'  => $request->country  ?? 'Venezuela',
            'timezone' => $request->timezone ?? 'UTC',
            'plan'     => $request->plan     ?? 'free',
            'status'   => 'active',
        ]);

        // Crear sucursal principal automáticamente
        Branch::create([
            'company_id' => $company->id,
            'name'       => $request->branch_name    ?? 'Principal',
            'address'    => $request->branch_address ?? $request->address ?? 'Por definir',
            'city'       => $request->city,
            'state'      => $request->state,
            'is_default' => true,
            'status'     => 'active',
        ]);

        return response()->json([
            'message' => 'Empresa creada exitosamente.',
            'company' => new CompanyResource($company->load('branches', 'defaultBranch')),
        ], 201);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function uniqueSlug(string $name): string
    {
        $slug  = Str::slug($name);
        $count = Company::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
