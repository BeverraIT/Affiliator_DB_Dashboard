<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Strip BOM dan normalize heading Excel agar import tidak gagal
        // akibat karakter tersembunyi di awal file
        HeadingRowFormatter::extend('clean_slug', function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value); // hapus BOM
            return Str::slug(trim($value), '_');
        });

        HeadingRowFormatter::default('clean_slug');
    }
}
