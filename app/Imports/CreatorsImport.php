<?php

namespace App\Imports;

use App\Models\Creator;
use App\Models\Category;
use App\Models\Age;
use App\Models\ContentStyle;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class CreatorsImport implements OnEachRow, WithHeadingRow, WithValidation
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $creator = Creator::updateOrCreate(
            ['username' => $row['username']],
            [
                'channel'        => $row['channel'],
                'gmv'            => $row['gmv'],
                'itemsterjual'   => $row['itemsterjual'],
                'genderpengikut' => $row['genderpengikut'],
                'namawa'         => $row['namawa'],
                'nomerwa'        => $row['nomerwa'],
            ],
        );

        if (!empty($row['kategori'])) {
            $categoryNames = explode(',', $row['kategori']);
            $categoryIds = [];

            foreach ($categoryNames as $name) {
                $cleanName = trim($name);

                if (!empty($cleanName)) {
                    $category = Category::firstOrCreate([
                        'name' => $cleanName,
                    ]);
                    $categoryIds[] = $category->id;
                }
            }

            $creator->categories()->sync($categoryIds);
        }

        if (!empty($row['umur'])) {
            $ageNames = explode(',', $row['umur']);
            $ageIds = [];

            foreach ($ageNames as $ageString) {
                $cleanAge = trim($ageString);

                if (!empty($cleanAge)) {
                    $age = Age::firstOrCreate([
                        'name' => $cleanAge,
                    ]);
                    $ageIds[] = $age->id;
                }
            }

            $creator->ages()->sync($ageIds);
        }

        if (!empty($row['konten_style'])) {
            $contentNames = explode(',', $row['konten_style']);
            $contentIds = [];

            foreach ($contentNames as $contentString) {
                $cleanContent = trim($contentString);

                if (!empty($cleanContent)) {
                    $content = ContentStyle::firstOrCreate([
                        'name' => $cleanContent,
                    ]);
                    $contentIds[] = $content->id;
                }
            }

            $creator->contentStyles()->sync($contentIds);
        }
    }

    public function rules(): array
    {
        return [
            'username'       => ['required'],
            'channel'        => ['required'],
            'gmv'            => ['required'],
            'itemsterjual'   => ['required'],
            'genderpengikut' => ['required'],
        ];
    }
}