<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\Coupon;
use App\Models\LaMission;
use App\Models\LaMissionComplete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    use MediaUpload;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (Auth::user()) {

            $xAxis = CarbonPeriod::create(
                Carbon::now()->subMonth(12)->format('Y-m-d'),
                '1 month',
                Carbon::now()->format('Y-m-d')
            );

            foreach ($xAxis as $year) {
                $user[] = User::whereYear('created_at', $year)->whereMonth('created_at', $year)->where(function ($query) {
                    $query->where('type', UserType::Student)->orwhere('type', null);
                })->count();
            }
            $allUsers = User::where('type', UserType::Student)->orWhere('type', null)->count();
            $monthStartDate = Carbon::now()->startOfMonth();
            $monthEndDate = Carbon::now()->endOfMonth();
            $userCurrentMonthCount =
                User::whereBetween('created_at', [$monthStartDate, $monthEndDate])
                ->where(function ($query) {
                    $query->where('type', UserType::Student)->orWhere('type', null);
                })->count();

            $totalMission = LaMission::count();
            $latestSubmittedMissions = LaMissionComplete::latest()->take(20)->get();
            $imageBaseUrl = $this->getBaseUrl();
            if ($request->type == "export") {
                $fileName = "missions.csv";
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );

                $columns = array(
                    'Sr No.', 'Student Name', 'Image', 'School Name', 'District Name', 'Block Name', 'Cluster Name', 'Activity Name', 'Submission Date', 'State', 'City'
                );

                $callback = function () use ($latestSubmittedMissions, $columns, $imageBaseUrl) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    $i = 1;
                    foreach ($latestSubmittedMissions as $latestSubmittedMission) {
                        $userImage = $latestSubmittedMission->user ? ($latestSubmittedMission->user->image_path ? $imageBaseUrl . $latestSubmittedMission->user->image_path : '') : '';
                        $row['sr_no']  = $i;
                        $row['student_name']  = $latestSubmittedMission->user->name ?? '';
                        $row['image']  =  $userImage;
                        $row['school_name']  = $latestSubmittedMission->user->school->name ?? '';
                        $row['district_name']  = $latestSubmittedMission->user->school->district ?? '';
                        $row['block_name']  = $latestSubmittedMission->user->school->block ?? '';
                        $row['cluster_name']  = $latestSubmittedMission->user->school->cluster ?? '';
                        $row['activity_name'] = $latestSubmittedMission->laMission->default_title ?? '';
                        $row['submission_date']  = date('d-m-Y', strtotime($latestSubmittedMission->created_at));
                        $row['state']  = $latestSubmittedMission->user->state ?? '';
                        $row['city']  =  $latestSubmittedMission->user->city ?? '';
                        fputcsv($file, array($row['sr_no'],  $row['student_name'], $row['image'], $row['school_name'], $row['district_name'], $row['block_name'], $row['cluster_name'], $row['activity_name'], $row['submission_date'], $row['state'], $row['city']));
                        $i++;
                    }
                    fclose($file);
                };
                return response()->stream($callback, 200, $headers);
            }
            return view('pages.admin.dashboard', [
                'users' => $allUsers,
                'xAxis' => $xAxis,
                'yAxis' => $user,
                "userCurrentMonthCount" => $userCurrentMonthCount,
                'totalMission' => $totalMission,
                'imageBaseUrl' => $imageBaseUrl,
                'latestSubmittedMissions' => $latestSubmittedMissions,
            ]);
        } else {
            return view('auth.login');
        }
    }
}
