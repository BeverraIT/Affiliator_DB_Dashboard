<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menjaga /kora-api/*: hanya KORA (membawa KORA_API_TOKEN) yang boleh masuk.
 * Token atau KORA_USER_ID belum diisi = API dianggap tidak ada.
 */
class KoraApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('kora.api_token');
        if ($token === '' || ! config('kora.user_id')) {
            abort(404);
        }

        $dikirim = (string) $request->bearerToken();
        if ($dikirim === '' || ! hash_equals($token, $dikirim)) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        return $next($request);
    }
}
