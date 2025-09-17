<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileTypeEnum;
use App\Enums\QuizGameParticipantStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\DistrictDataExportJob;
use App\Jobs\StudentExportJob;
use App\Models\DistrictDataFile;
use App\Models\LaMissionComplete;
use App\Models\LaQuizGameParticipant;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $states = User::whereNotNull('state')->groupBy('state')->pluck('state')->toArray();
        $schools = School::orderBy('name')->where('app_visible', StatusEnum::ACTIVE)->get();
        $charts['accurancy_chart']  = $charts['user_wise_mission_chart'] = $charts['user_wise_quiz_chart'] = $charts['mission_chart'] = [];
        if ($request->state != null  || $request->city != null || $request->school_id != null) {
            $charts['accurancy_chart']  = $this->accurancyChart($request);
            $charts['user_wise_mission_chart'] = $this->userWiseMissionsChart($request);
            $charts['user_wise_quiz_chart'] = $this->userWiseQuizChart($request);
            $charts['mission_chart'] = $this->missionsChart($request);
        }
        return view('pages.admin.statistics.index', compact('states', 'schools', 'charts', 'request'));
    }

    public function userWiseMissionsChart(Request $request)
    {
        return $this->generateChart($request, LaMissionComplete::class, 'user_count', 'user_id', '', 'user', 'name');
    }

    public function missionsChart(Request $request)
    {
        return $this->generateChart($request, LaMissionComplete::class, 'mission_count', 'la_mission_id', '', 'laMission', 'default_title');
    }

    public function userWiseQuizChart(Request $request)
    {
        return $this->generateChart($request, LaQuizGameParticipant::class, 'user_count', 'user_id', 'status', 'user', 'name');
    }

    public function accurancyChart(Request $request)
    {
        $fromDate = $request->days ? date("Y-m-d 23:59:59", strtotime("-" . $request->days . " days")) : null;

        $userMissions = LaMissionComplete::selectRaw('COUNT(*) as total_count, SUM(CASE WHEN approved_at IS NOT NULL THEN 1 ELSE 0 END) as approved_count')
            ->where(function ($query) use ($fromDate, $request) {
                if ($fromDate) {
                    $query->where('created_at', '>=', $fromDate);
                }

                $query->whereHas('user', function ($q) use ($request) {
                    if ($request->state) {
                        $q->where('state', $request->state);
                    }
                    if ($request->city) {
                        $q->where('city', $request->city);
                    }
                    if ($request->school_id) {
                        $q->where('school_id', $request->school_id);
                    }
                });
            })
            ->first();


        $accurancies[] = $userMissions->total_count;
        $accurancies[] = $userMissions->approved_count;

        foreach ($accurancies as $key => $accurancy) {
            $chart[$key]['x'] =  $key == 0 ? "total count" : "approved count";
            $chart[$key]['value'] = $accurancy;
            $chart[$key]['normal']['fill'] = Helper::rand_color();
        }
        return $chart;
    }

    public function generateChart(Request $request, $model, $countColumn, $groupByColumn, $statusColumn = null, $xField = null, $yField = null)
    {
        $chart = [];
        $fromDate = $request->days ? date("Y-m-d 23:59:59", strtotime("-" . $request->days . " days")) : null;

        $query = $model::query();
        if ($statusColumn != null) {
            $query->where($statusColumn, QuizGameParticipantStatusEnum::ACCEPT);
        }

        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }

        $query->whereHas('user', function ($q) use ($request) {
            if ($request->state) {
                $q->where('state', $request->state);
            }
            if ($request->city) {
                $q->where('city', $request->city);
            }
            if ($request->school_id) {
                $q->where('school_id', $request->school_id);
            }
        });

        $query->selectRaw('*, count(*) as ' . $countColumn)
            ->groupBy($groupByColumn);

        $query = $query->orderByDesc($countColumn)->get();

        if (count($query) > 0) {
            foreach ($query as $key => $item) {
                $chart[$key]['x'] = $item->$xField->$yField ?? null;
                $chart[$key]['value'] = $item->$countColumn;
                $chart[$key]['normal']['fill'] = Helper::rand_color();
            }
        }

        return $chart;
    }

    public function chhattisgarhStatus(Request $request)
    {
        $lastSavedFile = DistrictDataFile::where('file_type', FileTypeEnum::STUDENT)->latest()->first();
        $filePath = null;
        $file = null;
        if ($lastSavedFile) {
            $file = $lastSavedFile->name;
            if (Storage::exists("exports/" . $file)) {
                $filePath = $lastSavedFile->path;
            }
        }
        $schoolCounts = [
            'total_downloads' => User::where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12')
                ->whereHas('school', function ($query) {
                    $query->where('state', 'Chhattisgarh');
                })->count(),

            'school_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12');
                })->count(),

            'city_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12');
                })->whereNotNull('city')->distinct('city')->count('city'),

            'cluster_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12');
                })->whereNotNull('cluster')->distinct('cluster')->count('cluster'),

            'block_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12');
                })->whereNotNull('block')->distinct('block')->count('block'),

            'district_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')->where('created_at', '>', '2024-02-12');
                })->whereNotNull('district')->distinct('district')->count('district'),
        ];

        $missionSchoolCounts = [

            'total_downloads' => User::where('state', 'Chhattisgarh')
                ->where('created_at', '>', '2024-02-12')
                ->whereHas('laMissionCompletes', function ($query) {
                    $query->where('la_mission_id', 1);
                })->count(),

            'school_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('created_at', '>', '2024-02-12')
                        ->whereHas('laMissionCompletes', function ($subQuery) {
                            $subQuery->where('la_mission_id', 1);
                        });
                })->count(),

            'city_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('created_at', '>', '2024-02-12')
                        ->whereHas('laMissionCompletes', function ($subQuery) {
                            $subQuery->where('la_mission_id', 1);
                        });
                })->whereNotNull('city')->distinct('city')->count('city'),

            'cluster_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('created_at', '>', '2024-02-12')
                        ->whereHas('laMissionCompletes', function ($subQuery) {
                            $subQuery->where('la_mission_id', 1);
                        });
                })->whereNotNull('cluster')->distinct('cluster')->count('cluster'),

            'block_count' => School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('created_at', '>', '2024-02-12')
                        ->whereHas('laMissionCompletes', function ($subQuery) {
                            $subQuery->where('la_mission_id', 1);
                        });
                })->whereNotNull('block')->distinct('block')->count('block'),

            'district_count' =>  School::where('status', StatusEnum::ACTIVE)
                ->where('app_visible', StatusEnum::ACTIVE)
                ->where('state', 'Chhattisgarh')
                ->whereHas('users', function ($query) {
                    $query->where('state', 'Chhattisgarh')
                        ->where('created_at', '>', '2024-02-12')
                        ->whereHas('laMissionCompletes', function ($subQuery) {
                            $subQuery->where('la_mission_id', 1);
                        });
                })->whereNotNull('district')->distinct('district')->count('district'),
        ];

        return view('pages.admin.statistics.status', compact('schoolCounts', 'missionSchoolCounts', 'filePath'));
    }

    public function districtStatus(Request $request)
    {
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
            ->whereNotNull('district')->groupBy('district')->orderBy('district', 'asc')->paginate(10);

        $lastSavedFile = DistrictDataFile::where('file_type', FileTypeEnum::DISTRICT)->latest()->first();
        $filePath = null;
        $file = null;
        if ($lastSavedFile) {
            $file = $lastSavedFile->name;
            if (Storage::exists("exports/" . $file)) {
                $filePath = $lastSavedFile->path;
            }
        }
        if ($request->type == 'export') {
            if (Storage::exists("exports/" . $file)) {
                Storage::delete("exports/" . $file);
            }
            $file = "chhattisgarh_district_data_" . time() . ".xlsx";
            DistrictDataFile::create(
                [
                    'name' => $file,
                    'path' => "exports/" . $file,
                    'file_type' =>  FileTypeEnum::DISTRICT,
                ]
            );
            dispatch(new DistrictDataExportJob($file));
            return redirect()->back()->with('success', " Generating the report. Please come after some time");
        }

        return view('pages.admin.statistics.district-status', compact('schools', 'filePath'));
    }

    public function chhattisgarhStudentExport()
    {
        $lastSavedFile = DistrictDataFile::where('file_type', FileTypeEnum::STUDENT)->latest()->first();
        if ($lastSavedFile) {
            $file = $lastSavedFile->name;
            if (Storage::exists("exports/" . $file)) {
                Storage::delete("exports/" . $file);
            }
        }
        $file = "chhattisgarh_student_data_" . time() . ".xlsx";
        DistrictDataFile::create(
            [
                'name' => $file,
                'path' => "exports/" . $file,
                'file_type' =>  FileTypeEnum::STUDENT,
            ]
        );
        dispatch(new StudentExportJob($file));
        return redirect()->back()->with('success', "Generating the report. Please come after some time");
    }

    public function barGraph(Request $request)
    {
        if ($request->type == 'monthly_count') {
            $monthlyData = $this->monthlyCountGraph($request);
            if ($request->ajax()) {
                return response()->json($monthlyData);
            }
            return view('pages.admin.statistics.barcharts.month-wise', compact('monthlyData', 'request'));
        }

        $gradeCountData = $this->gradeCountGraph();
        $totalCount = $this->totalCountGraph();
        return view('pages.admin.statistics.barcharts.downloads', compact('totalCount',  'gradeCountData', 'request'));
    }

    public function totalCountGraph()
    {
        $data = [
            'labels' => ['Student', 'Teacher', 'Mentor'],
            'datasets' => []
        ];

        $categories = [UserType::Student, UserType::Teacher, UserType::Mentor];
        $isLifeApps = ['all', StatusEnum::YES, StatusEnum::NO];

        $counts = [];

        foreach ($categories as $category) {
            foreach ($isLifeApps as $isLifeApp) {
                $countQuery = User::query()->where('type', $category);
                if ($isLifeApp !== 'all') {
                    $countQuery->whereHas('school', function ($query) use ($isLifeApp) {
                        $query->where('is_life_lab', $isLifeApp);
                    });
                }
                $counts[$category][$isLifeApp] = $countQuery->count();
            }
        }

        foreach ($isLifeApps as $isLifeApp) {
            $dataSet = [
                'label' => $isLifeApp === 'all' ? 'All' : ($isLifeApp == 1 ? 'Life-Lab User' : 'Other User'),
                'data' => [],
                'backgroundColor' => Helper::rand_color(),
            ];

            foreach ($categories as $category) {
                $dataSet['data'][] = $counts[$category][$isLifeApp];
            }

            $data['datasets'][] = $dataSet;
            $data['total_download_count'] = User::whereIn('type', [UserType::Student, UserType::Teacher, UserType::Mentor])->count();
        }
        return $data;
    }

    public function monthlyCountGraph(Request $request)
    {
        $date = $request->month_year ?? Carbon::now()->format('Y-m');
        $categories = ['All', UserType::Student, UserType::Teacher, UserType::Mentor];

        $counts = [];

        foreach ($categories as $category) {
            $startOfMonth = Carbon::createFromFormat('Y-m', $date)->firstOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $date)->endOfMonth();
            while ($startOfMonth <= $endOfMonth) {

                $startOfWeek = $startOfMonth->copy()->format('m');
                $endOfWeek = $startOfMonth->copy()->addWeek()->format('m');

                if ($startOfWeek == $endOfWeek) {
                    $endOfWeek = $startOfMonth->copy()->addWeek()->format('Y-m-d');
                    $endOfWeekKey = $startOfMonth->copy()->addWeek()->format('d');
                } else {
                    $endOfWeek = $startOfMonth->copy()->endOfMonth()->format('Y-m-d');
                    $endOfWeekKey = $startOfMonth->copy()->endOfMonth()->format('d');
                }

                $weekKey = $startOfMonth->format('d') . ' - ' . $endOfWeekKey . ' ' . $startOfMonth->format('M');

                $users = User::whereBetween('created_at', [$startOfMonth, $endOfWeek]);
                if ($category == 'All') {
                    $userCount = $users->whereIn('type', [UserType::Student, UserType::Teacher, UserType::Mentor])->count();
                } else {
                    $userCount = $users->where('type', $category)->count();
                }

                $counts[$category][$weekKey] = $userCount;
                $startOfMonth->addWeek()->addDay(1);
            }
        }
        $monthlyData = [
            'labels' => array_keys($counts[current($categories)]),
            'datasets' => [],
        ];

        foreach ($categories as $category) {
            if ($category == 'All') {
                $label = 'All';
            } elseif ($category == UserType::Student) {
                $label = 'Student';
            } elseif ($category == UserType::Teacher) {
                $label = 'Teacher';
            } else {
                $label = 'Mentor';
            }

            $dataSet = [
                'label' => $label,
                'data' => array_values($counts[$category]),
                'backgroundColor' => Helper::rand_color(),
            ];
            $monthlyData['datasets'][] = $dataSet;
            $monthlyData['total_count'] =  User::whereIn('type', [UserType::Student, UserType::Teacher, UserType::Mentor])->whereMonth('created_at', $endOfMonth->copy()->format('m'))->whereYear('created_at', $endOfMonth->copy()->format('Y'))->count();
        }
        return $monthlyData;
    }

    public function gradeCountGraph()
    {
        $gradeCountData = [
            'labels' => ["Grade 1", "Grade 2", "Grade 3", "Grade 4", "Grade 5", "Grade 6", "Grade 7", "Grade 8", "Grade 9", "Grade 10"],
            'datasets' => []
        ];

        $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $isLifeApps = ['all', StatusEnum::YES, StatusEnum::NO];

        $counts = [];

        foreach ($grades as $grade) {
            foreach ($isLifeApps as $isLifeApp) {
                $countQuery = User::query()->where('grade', $grade);
                if ($isLifeApp !== 'all') {
                    $countQuery->whereHas('school', function ($query) use ($isLifeApp) {
                        $query->where('is_life_lab', $isLifeApp);
                    });
                }
                $counts[$grade][$isLifeApp] = $countQuery->count();
            }
        }

        foreach ($isLifeApps as $isLifeApp) {
            $dataSet = [
                'label' => $isLifeApp === 'all' ? 'All' : ($isLifeApp == 1 ? 'Life-Lab User' : 'Other User'),
                'data' => [],
                'backgroundColor' => Helper::rand_color(),
            ];

            foreach ($grades as $category) {
                $dataSet['data'][] = $counts[$category][$isLifeApp];
            }

            $gradeCountData['datasets'][] = $dataSet;
            $gradeCountData['total_download_count'] = User::whereIn('grade', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->count();
        }
        return $gradeCountData;
    }
}
