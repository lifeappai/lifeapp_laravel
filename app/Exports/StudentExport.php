<?php

namespace App\Exports;

use App\Enums\UserType;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;

class StudentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        try {
            $result = [];

            $i = 1;
            User::where('state', 'Chhattisgarh')
                ->where('created_at', '>', '2024-02-12')
                ->whereHas('school', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('type', UserType::Student);
                })->orderBy("id", "asc")->chunk(500, function ($users) use (&$i, &$result) {
                    Log::info("fetched: $i - " . ($i + 500));
                    foreach ($users as $user) {
                        $data = [
                            $i,
                            $user->name ?? '-',
                            $user->laGrade->name ?? '',
                            $user->school ? $user->school->district : '-',
                            $user->school ? $user->school->block : '-',
                            $user->school ? $user->school->cluster : '-',
                            $user->city ?? '-',
                            $user->school ? $user->school->name : '-',
                            $user->laMissionCompletes->count() ?? '0',
                            $user->created_at ? $user->created_at->format('d-m-Y') : '-',
                        ];
                        $i++;
                        $result[] = $data;
                    }
                });
            return collect($result);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function headings(): array
    {
        return ['Sr No.', 'Student Name', 'Grade', 'District Name', 'Block Name', 'Cluster Name', 'City', 'School',  'Mission Attempted', 'Register Date'];
    }
}
