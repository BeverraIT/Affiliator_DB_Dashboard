<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Validasi redirect_to dari form agar tidak bisa dipakai open redirect ke domain luar.
 */
trait RedirectsSafely
{
    /**
     * Redirect ke redirect_to hanya jika masih di dalam aplikasi; selain itu fallback ke $fallbackRoute.
     */
    protected function redirectSafely(Request $request, string $fallbackRoute): RedirectResponse
    {
        $target = (string) $request->input('redirect_to', '');
        $base = rtrim(url('/'), '/');

        // Path relatif internal ("/admin/..."), atau URL absolut yang benar-benar
        // di bawah base URL aplikasi ("//evil.com" dan "http://host.evil.com" ditolak).
        $isRelative = str_starts_with($target, '/') && ! str_starts_with($target, '//');
        $isInternal = $target === $base
            || str_starts_with($target, $base.'/')
            || str_starts_with($target, $base.'?');

        if ($target !== '' && ($isRelative || $isInternal)) {
            return redirect($target);
        }

        return redirect()->route($fallbackRoute);
    }
}
