<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Menyatukan resolusi parameter sorting (sort/dir) untuk halaman daftar.
 */
trait ResolvesListQuery
{
    /**
     * Validasi kolom sort terhadap whitelist dan arah sort terhadap asc/desc.
     *
     * @param  array<string>  $allowed
     * @return array{0:string,1:string}  [$sort, $dir]
     */
    protected function resolveSort(Request $request, array $allowed, string $default, string $defaultDir = 'asc'): array
    {
        $sort = (string) $request->query('sort', $default);
        if (! in_array($sort, $allowed, true)) {
            $sort = $default;
        }

        $dir = strtolower((string) $request->query('dir', $defaultDir)) === 'desc' ? 'desc' : 'asc';

        return [$sort, $dir];
    }
}
