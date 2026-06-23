<?php

namespace App\Imports;

use App\Models\Creator;
use App\Models\Category;
use App\Models\Age;
use App\Models\ContentStyle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class CreatorsImport implements OnEachRow, WithHeadingRow, WithValidation, WithChunkReading
{
    /** Peta name=>id di-cache agar tidak firstOrCreate berulang tiap baris. */
    protected Collection $categoryMap;
    protected Collection $ageMap;
    protected Collection $styleMap;

    /** Set username yang sudah ada (cek duplikat in-memory, bukan query/baris). */
    protected Collection $existing;

    public int $imported = 0;
    public int $skipped = 0;

    public function __construct()
    {
        $this->categoryMap = Category::pluck('id', 'name');
        $this->ageMap      = Age::pluck('id', 'name');
        $this->styleMap    = ContentStyle::pluck('id', 'name');
        $this->existing    = Creator::pluck('username')->flip();
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = preg_replace('/\s+/', ' ', trim($value));
            }
        }

        // Lewati duplikat tanpa query per baris.
        if ($this->existing->has($row['username'])) {
            $this->skipped++;
            return;
        }

        $creator = Creator::create([
            'username'       => $row['username'],
            'channel'        => $row['channel'],
            'gmv'            => $row['gmv'],
            'itemsterjual'   => $row['itemsterjual'],
            'genderpengikut' => $row['genderpengikut'],
            'namawa'         => $row['namawa'] ?? null,
            'nomerwa'        => $row['nomerwa'] ?? null,
            'user_id'        => auth()->id(),
        ]);

        $this->existing->put($row['username'], true);
        $this->imported++;

        $this->syncRelation($creator, $row['kategori'] ?? null, 'categories', $this->categoryMap, Category::class);
        $this->syncRelation($creator, $row['umur'] ?? null, 'ages', $this->ageMap, Age::class);
        $this->syncRelation($creator, $row['konten_style'] ?? null, 'contentStyles', $this->styleMap, ContentStyle::class);
    }

    /**
     * Pisah CSV nama, resolve id dari cache (firstOrCreate hanya untuk nama baru),
     * lalu sync ke relasi pivot kreator.
     */
    private function syncRelation(Creator $creator, ?string $csv, string $relation, Collection $map, string $modelClass): void
    {
        if (empty($csv)) {
            return;
        }

        $ids = [];
        foreach (explode(',', $csv) as $raw) {
            $name = trim($raw);
            if ($name === '') {
                continue;
            }
            $id = $map->get($name);
            if (! $id) {
                $id = $modelClass::firstOrCreate(['name' => $name])->id;
                $map->put($name, $id);
            }
            $ids[] = $id;
        }

        $creator->{$relation}()->sync($ids);
    }

    public function rules(): array
    {
        return [
            // username harus berupa handle (tanpa spasi/koma) — mencegah string
            // kategori nyasar ke kolom username akibat kolom CSV tidak sejajar.
            'username'       => ['required', 'regex:/^[^\s,]+$/'],
            'channel'        => ['required'],
            'gmv'            => ['required'],
            'itemsterjual'   => ['required'],
            'genderpengikut' => ['required'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'username.regex' => 'Username tidak boleh mengandung spasi atau koma (pastikan kolom tidak tertukar dengan kategori).',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
