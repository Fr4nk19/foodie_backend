<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * GET /api/v1/users
     *
     * List all users (super_admin).
     * Optionally filter by ?company_id=X or ?role=Y.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['company', 'branch'])->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json([
            'data' => UserResource::collection($query->get()),
        ]);
    }

    /**
     * POST /api/v1/users
     *
     * Create a new user (super_admin).
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'company_id' => $request->company_id,
            'branch_id'  => $request->branch_id,
            'is_active'  => $request->input('is_active', true),
            'job_type'   => $request->job_type,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente.',
            'data'    => new UserResource($user->load(['company', 'branch'])),
        ], 201);
    }

    /**
     * GET /api/v1/users/{user}
     *
     * Show a single user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user->load(['company', 'branch'])),
        ]);
    }

    /**
     * PUT /api/v1/users/{user}
     *
     * Update a user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['name', 'email', 'role', 'company_id', 'branch_id', 'is_active', 'job_type']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente.',
            'data'    => new UserResource($user->fresh()->load(['company', 'branch'])),
        ]);
    }

    /**
     * DELETE /api/v1/users/{user}
     *
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting super_admin accounts
        if ($user->isSuperAdmin()) {
            return response()->json([
                'message' => 'No se puede eliminar un super administrador.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente.',
        ]);
    }

    // ─── Company-scoped endpoints ─────────────────────────────────────────────

    /**
     * GET /api/v1/companies/{company}/users
     *
     * List users belonging to a company (company_admin_or_super).
     */
    public function indexByCompany(Request $request, Company $company): JsonResponse
    {
        $query = $company->users()->with(['branch'])->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return response()->json([
            'data' => UserResource::collection($query->get()),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/users
     *
     * Create a user for a specific company (company_admin_or_super).
     * company_admin can only assign roles: branch_manager, employee.
     */
    public function storeForCompany(StoreUserRequest $request, Company $company): JsonResponse
    {
        $authUser = $request->user();

        // company_admin cannot create company_admin or super_admin accounts
        if ($authUser->isCompanyAdmin() && in_array($request->role, ['company_admin', 'super_admin'])) {
            return response()->json([
                'message' => 'No tienes permiso para asignar ese rol.',
            ], 403);
        }

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role ?? 'employee',
            'company_id' => $company->id,
            'branch_id'  => $request->branch_id,
            'is_active'  => $request->input('is_active', true),
            'job_type'   => $request->job_type,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente.',
            'data'    => new UserResource($user->load(['company', 'branch'])),
        ], 201);
    }
}
