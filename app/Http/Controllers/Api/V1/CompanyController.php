<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\StoreCompanyRequest;
use App\Http\Requests\Api\V1\Company\UpdateCompanyRequest;
use App\Http\Resources\Api\V1\CompanyResource;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * GET /api/v1/companies
     *
     * List companies with pagination.
     * Query params:
     *   - per_page: int (default 15, max 100)
     *   - page:     int (default 1)
     *
     * Requires: auth:sanctum + super_admin role.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        $paginator = Company::with('branches', 'defaultBranch')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => CompanyResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem() ?? 0,
                'to'           => $paginator->lastItem()  ?? 0,
            ],
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

    /**
     * GET /api/v1/companies/{company}
     *
     * Show a single company with its branches.
     * Requires: auth:sanctum + super_admin role.
     */
    public function show(Company $company): JsonResponse
    {
        $company->load('branches', 'defaultBranch');

        return response()->json([
            'company' => new CompanyResource($company),
        ]);
    }

    /**
     * PUT /api/v1/companies/{company}
     *
     * Update a company's data.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return response()->json([
            'message' => 'Empresa actualizada exitosamente.',
            'company' => new CompanyResource($company->fresh()->load('branches', 'defaultBranch')),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function uniqueSlug(string $name): string
    {
        $slug  = Str::slug($name);
        $count = Company::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
