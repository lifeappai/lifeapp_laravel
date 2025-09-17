<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Enums\StatusEnum;
use App\Models\School;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistrictDataExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        $result = [];
        $schools = School::where('status', StatusEnum::ACTIVE)
            ->where('app_visible', StatusEnum::ACTIVE)
            ->where('state', 'Chhattisgarh')
            ->whereHas('users', function ($query) {
                $query->where('state', 'Chhattisgarh')
                    ->where('created_at', '>', '2024-02-12')
                    ->whereHas('laMissionCompletes', function ($subQuery) {
                        $subQuery->where('la_mission_id', 1);
                    });
            })
            ->whereNotNull('district')->groupBy('district')->orderBy('district', 'asc')->get();
        $i = 1;
        $totalSchools = 0;
        $totalEnrolledSchools = 0;
        foreach ($schools as $school) {
            $totalSchoolCount = $school->getDistrictCount($school->district, 'Chhattisgarh');
            $totalSchools += $totalSchoolCount;
            $totalSchoolEnrolledCount = $school->getUserCount($school->district, 'Chhattisgarh', '2024-02-12');
            $totalEnrolledSchools += $totalSchoolEnrolledCount;
            $data = [
                $i,
                $school->district,
                $totalSchoolCount,
                $totalSchoolEnrolledCount,
                $totalSchoolEnrolledCount != 0 ? number_format(($totalSchoolEnrolledCount / $totalSchoolCount) * 100, 2) . '%' : '-',
            ];
            $i++;
            $result[] = $data;
        }
        $result[] = [
            '',
            'Total',
            $totalSchools,
            $totalEnrolledSchools,
            $totalEnrolledSchools != 0 ? number_format(($totalEnrolledSchools / $totalSchools) * 100, 2) . '%' : '-',
        ];
        return collect($result);
    }

    public function headings(): array
    {
        return ['Sr No.', 'District', 'Total no. of Schools', 'Schools Enrolled', '% Enrolled'];
    }
}
