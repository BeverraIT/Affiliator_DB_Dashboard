<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;

/**
 * Menyatukan logika bulk-destroy yang sebelumnya diduplikasi di banyak controller.
 */
trait HandlesBulkDelete
{
    /**
     * Hapus banyak record sekaligus, lalu redirect back dengan flash message.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     * @param  array<int|string>  $ids
     */
    protected function bulkDelete(string $modelClass, array $ids, string $label): RedirectResponse
    {
        try {
            $modelClass::whereIn('id', $ids)->delete();

            return back()->with('success', count($ids) . " {$label} berhasil dihapus.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
