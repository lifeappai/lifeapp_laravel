<?php

namespace App\Exports;

use App\Models\User;
use App\Enums\UserType;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithMapping, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    
    public function query()
    {
        return User::query()
            ->select([
                'id',
                'name',
                'mobile_no',
                'type',
                'address',
                'state',
                'city',
                'dob',
                'created_at',
                'earn_coins',
                'school_id',
                'la_grade_id',
                'la_section_id',
                'image_path',
            ])
            ->with([
                'school:id,name,code,district,block,cluster',
                'laGrade:id,name',
                'laSection:id,name',
                'laMissionApproved:id,user_id',
                'laMissionRequests:id,user_id',
                'laQuizGameResults:id,user_id',
                'couponRedeems:id,user_id,coins',
                'laSubjectCouponCodes:id,user_id',
            ]);
    }

    public function map($user): array
    {
        $imageBaseUrl = "https://media.gappubobo.com/";

        return [
            $user->id, // Sr No. (auto-increment logic handled in Excel)
            $user->name,
            $user->image_path ? $imageBaseUrl . $user->image_path : '',
            $user->mobile_no,
            $user->school->name ?? '',
            $user->school->code ?? '',
            $user->school->district ?? '',
            $user->school->block ?? '',
            $user->school->cluster ?? '',
            $user->laGrade->name ?? '',
            $user->laSection->name ?? '',
            $user->type == UserType::Student ? 'Student' : ($user->type == UserType::Teacher ? 'Teacher' : ($user->type == UserType::Mentor ? 'Mentor' : '-')),
            $user->address,
            $user->state,
            $user->city,
            $user->dob ? date("d-m-Y", strtotime($user->dob)) : '-',
            $user->laMissionApproved->count() ?? 0,
            $user->laMissionRequests->count() ?? 0,
            $user->laQuizGameResults->count() ?? 0,
            $user->earn_coins ?? 0,
            $user->earnCoinsByType('quiz') ?? 0,
            $user->earnCoinsByType('mission') ?? 0,
            $user->couponRedeems->sum('coins') ?? 0,
            $user->laSubjectCouponCodes->count() ?? 0,
            $user->user_rank ?? '-',
            $user->created_at ? $user->created_at->format('d-m-Y') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Sr No.',
            'Student Name',
            'Image',
            'Mobile Number',
            'School',
            'School Code',
            'District Name',
            'Block Name',
            'Cluster Name',
            'Grade',
            'Section',
            'Type',
            'Address',
            'State',
            'City',
            'DOB',
            'Mission Completed',
            'Mission Requested',
            'Quiz',
            'Earn Coins',
            'Quiz Coins',
            'Mission Coins',
            'Coins Redeemed',
            'Product Redeemed',
            'Rating',
            'Register Date',
        ];
    }
}
