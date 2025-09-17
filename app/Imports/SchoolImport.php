<?php

namespace App\Imports;

use App\Enums\StatusEnum;
use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SchoolImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($key == 0) continue;
            
            $appVisible = strtolower(trim($row[8]));
            $isLifeLab = strtolower(trim($row[9]));

            School::updateOrCreate(
                [
                    'name' => $row[0],
                    'state' => $row[1],
                    'city' => $row[2],
                    'district' => $row[3],
                    'block' => $row[4],
                    'cluster' => $row[5],
                    'pin_code' => $row[6],
                ],
                [
                    'code' => $row[7],
                    'app_visible' => $appVisible == 'yes' ? StatusEnum::ACTIVE : StatusEnum::DEACTIVE,
                    'is_life_lab' => $isLifeLab == 'yes' ? StatusEnum::YES : StatusEnum::NO,
                ]
            );
        }
    }
}