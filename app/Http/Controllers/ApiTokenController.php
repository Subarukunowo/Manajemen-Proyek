<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    /**
     * POST /api/auth/token
     * Login & dapatkan Sanctum token.
     */
    public function issue(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password ?? '')) {
            AuditLog::record('api_login_failed', 'Failed API login for: ' . $data['email']);
            return response()->json(['message' => 'Kredensial tidak valid.'], 401);
        }

        $deviceName = $data['device_name'] ?? 'api-client';
        $token      = $user->createToken($deviceName)->plainTextToken;

        AuditLog::record('api_login', "API token issued for {$user->email} [{$deviceName}]");

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * DELETE /api/auth/token
     * Revoke token aktif (logout API).
     */
    public function revoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        AuditLog::record('api_logout', 'API token revoked for ' . $request->user()->email);

        return response()->json(['message' => 'Token berhasil di-revoke.']);
    }
}
