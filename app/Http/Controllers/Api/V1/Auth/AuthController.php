<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterSuperAdminRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/login
     *
     * Authenticate any user (super admin, company admin, branch manager or employee)
     * and return a Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso.',
            'token'   => $token,
            'user'    => new UserResource($user->load('company', 'branch')),
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     *
     * Revoke the current API token.
     * Requires: auth:sanctum middleware.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }

    /**
     * POST /api/v1/auth/super-admin
     *
     * Register the platform super admin.
     * Only allowed when no super admin exists yet (initial setup).
     */
    public function registerSuperAdmin(RegisterSuperAdminRequest $request): JsonResponse
    {
        $superAdminExists = User::where('role', 'super_admin')->exists();

        if ($superAdminExists) {
            return response()->json([
                'message' => 'El super administrador ya fue registrado.',
            ], 409);
        }

        $superAdmin = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password, // cast 'hashed' en el modelo
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        $token = $superAdmin->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Super administrador registrado exitosamente.',
            'token'   => $token,
            'user'    => new UserResource($superAdmin),
        ], 201);
    }
}
