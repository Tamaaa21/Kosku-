<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // If user is pemilik, ensure their ownership verification is verified
        if ($user->role === 'pemilik' && $user->status_verifikasi !== 'verified') {
            return response()->json([
                'message' => 'Fitur ini hanya dapat diakses oleh pemilik kos yang telah terverifikasi. Silakan lengkapi verifikasi kepemilikan properti terlebih dahulu.',
                'status_verifikasi' => $user->status_verifikasi ?? 'unverified',
            ], 403);
        }

        return $next($request);
    }
}
