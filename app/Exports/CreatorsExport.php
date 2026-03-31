<?php

namespace App\Exports;

use App\Models\Creator;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class CreatorsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $q = (string) $this->request->query('q', '');
        $filter_umur = $this->request->query('filter_umur', []);
        $filter_kategori = $this->request->query('filter_kategori', []);
        $filter_style = $this->request->query('filter_style', []);
        $min_gmv = $this->request->query('min_gmv');
        $max_gmv = $this->request->query('max_gmv');
        $sort = (string) $this->request->query('sort', 'username');
        $dir = strtolower((string) $this->request->query('dir', 'desc')) == 'asc' ? 'asc' : 'desc';

        $query = Creator::query()
            ->with([
                'contentStyles',
                'categories',
                'ages'
            ]);

        // Filter Umur
        if (!empty($filter_umur)) {
            $query->whereHas('ages', function ($q) use ($filter_umur) {
                $q->whereIn('ages.id', $filter_umur);
            });
        }

        // Filter Kategori
        if (!empty($filter_kategori)) {
            $query->whereHas('categories', function ($q) use ($filter_kategori) {
                $q->whereIn('categories.id', $filter_kategori);
            });
        }

        // Filter Content Style
        if (!empty($filter_style)) {
            $query->whereHas('contentStyles', function ($q) use ($filter_style) {
                $q->whereIn('contentstyles.id', $filter_style);
            });
        }

        // Filter GMV
        $query->when($min_gmv, function ($q) use ($min_gmv) {
            $cleanMin = preg_replace('/[^0-9]/', '', $min_gmv);
            $q->where('gmv', '>=', $cleanMin);
        });

        // Filter GMV
        $query->when($max_gmv, function ($q) use ($max_gmv) {
            $cleanMax = preg_replace('/[^0-9]/', '', $max_gmv);
            $q->where('gmv', '<=', $cleanMax);
        });

        // Filter Top Kreator
        if ( $this->request->has('filter_top_creator')) {
            $query->where('top_creator', 1);
        }

        // Filter Kreator Diblokir
        if ( $this->request->has('filter_is_blocked')) {
            $query->where('is_blocked', 1);
        }

        if ($q) {
            $query->where('username', 'like', '%' . $q . '%');
        }

        return $query->orderBy($sort, $dir);
    }

    public function headings(): array
    {
        return [
            'username',
            'channel',
            'gmv',
            'itemsterjual',
            'genderpengikut',
            'namawa',
            'nomerwa',
            'kategori',
            'umur',
            'konten_style',
        ];
    }

    public function map($creator): array
    {
        return [
            $creator->username,
            $creator->channel,
            $creator->gmv,
            $creator->itemsterjual,
            $creator->genderpengikut,
            $creator->namawa,
            $creator->nomerwa,
            $creator->categories->pluck('name')->implode(', '),
            $creator->ages->pluck('name')->implode(', '),
            $creator->contentStyles->pluck('name')->implode(', '),
        ];
    }
}