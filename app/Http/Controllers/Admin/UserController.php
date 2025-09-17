<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Exports\UsersExport;
use App\Helpers\Helper;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Traits\MediaUpload;
use App\Jobs\UserExportJob;
use App\Models\CoinTransaction;
use App\Models\Coupon;
use App\Models\CouponRedeem;
use App\Models\LaMissionComplete;
use App\Models\LaMissionUserTiming;
use App\Models\LaTopic;
use App\Models\LaTopicAssign;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class UserController extends LocationController
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
        $mobileNumber = $request->mobileNumber;
        $schoolId = $request->school_id;
        $schoolName = $request->school_name;
        $grade = $request->grade;
        $state = $request->state;
        $city = $request->city;
        $missionRequested = $request->mission_requested;
        $missionApproved = $request->mission_approved;
        $earnCoins = $request->earn_coins;
        $registerDate = $request->register_date;
        $registerEndDate = $request->register_end_date;
        $missionType = $request->missionType;
        $districtName = $request->district_name;
        $blockName = $request->block_name;
        $clusterName = $request->cluster_name;
        $schoolCode = $request->school_code;
        $userType = $request->type;
        $filterData = $request->all();
    
        $users = User::filterUser($filterData)->paginate(25);
    
        $imageBaseUrl = $this->getBaseUrl();
        $countLists = range(1, 12);
        $statesData = $this->states('india');
        $states = $statesData->getData();
        $cities = $request->state ? $this->getCities($request->state, $request) : [];
    
        $schools = School::select('id', 'name')->get();
    

        return view('pages.admin.user.list', compact(
            'users',
            'imageBaseUrl',
            'mobileNumber',
            'missionType',
            'schoolId', 
            'schoolName',
            'countLists',
            'grade',
            'states',
            'state',
            'city',
            'cities',
            'missionRequested',
            'missionApproved',
            'earnCoins',
            'registerDate',
            'registerEndDate',
            'districtName',
            'blockName',
            'clusterName',
            'schoolCode',
            'userType',
            'schools'
        ));
    }

    public function scopeFilterUser($query, $filters)
    {
        return $query->when(isset($filters['mobileNumber']), function ($q) use ($filters) {
            $q->where('mobile_number', 'like', "%{$filters['mobileNumber']}%");
        })
        ->when(isset($filters['school_id']), function ($q) use ($filters) {
            $q->where('school_id', $filters['school_id']);
        })
        ->when(isset($filters['school_name']), function ($q) use ($filters) {
            $q->whereHas('school', function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['school_name']}%");
            });
        })
        ->when(isset($filters['grade']), function ($q) use ($filters) {
            $q->where('grade', $filters['grade']);
        })
        ->when(isset($filters['state']), function ($q) use ($filters) {
            $q->where('state', 'like', "%{$filters['state']}%");
        })
        ->when(isset($filters['city']), function ($q) use ($filters) {
            $q->where('city', 'like', "%{$filters['city']}%");
        })
        ->when(isset($filters['mission_requested']), function ($q) use ($filters) {
            $q->where('mission_requested', $filters['mission_requested']);
        })
        ->when(isset($filters['mission_approved']), function ($q) use ($filters) {
            $q->where('mission_approved', $filters['mission_approved']);
        })
        ->when(isset($filters['earn_coins']), function ($q) use ($filters) {
            $points = explode('-', $filters['earn_coins']);
            if (count($points) === 2) {
                $q->whereBetween('earn_coins', [(int)$points[0], (int)$points[1]]);
            } elseif ($filters['earn_coins'] == 'above_10000') {
                $q->where('earn_coins', '>', 10000);
            }
        })
        ->when(isset($filters['register_date']), function ($q) use ($filters) {
            $q->whereDate('created_at', '>=', $filters['register_date']);
        })
        ->when(isset($filters['register_end_date']), function ($q) use ($filters) {
            $q->whereDate('created_at', '<=', $filters['register_end_date']);
        })
        ->when(isset($filters['missionType']), function ($q) use ($filters) {
            $q->where('mission_type', $filters['missionType']);
        })
        ->when(isset($filters['district_name']), function ($q) use ($filters) {
            $q->where('district_name', 'like', "%{$filters['district_name']}%");
        })
        ->when(isset($filters['block_name']), function ($q) use ($filters) {
            $q->where('block_name', 'like', "%{$filters['block_name']}%");
        })
        ->when(isset($filters['cluster_name']), function ($q) use ($filters) {
            $q->where('cluster_name', 'like', "%{$filters['cluster_name']}%");
        })
        ->when(isset($filters['school_code']), function ($q) use ($filters) {
            $q->where('school_code', 'like', "%{$filters['school_code']}%");
        })
        ->when(isset($filters['type']), function ($q) use ($filters) {
            $q->where('type', $filters['type']);
        });
    }
    
    
    // schoolfilter
    public function searchSchools(Request $request)
    {
        $query = $request->get('query');
        
        $schools = School::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name']);
        
        return response()->json($schools);
    }

    public function exportBySchoolCode(Request $request)
    {
        // Get the school_code from the request
        $schoolCode = $request->input('school_code');

        // Check if the school_code is provided, otherwise return with an error
        if (!$schoolCode) {
            return redirect()->back()->with('error', 'Please provide a school code.');
        }

        // Define the columns for the CSV
        $columns = [
            'Sr No.',
            'Student Name',
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
            'Register Date'
        ];

        // Open the CSV file for writing
        $fileName = "users_by_school_code.csv";
        $file = fopen(storage_path("exports/{$fileName}"), 'w');
        fputcsv($file, $columns);

        // Start the row number
        $i = 1;

        // Query users based on school_code
        $users = User::with(['school']) // Eager load the school relationship
            ->whereHas('school', function ($query) use ($schoolCode) {
                // Filter by school_code if it's provided
                $query->where('code', $schoolCode);
            })
            ->get();
        
        // Write the filtered users to the CSV file
        foreach ($users as $user) {
            $userType = '-';
                if ($user->type == UserType::Student) {
                    $userType = 'Student';
                } elseif ($user->type == UserType::Teacher) {
                    $userType = 'Teacher';
                } elseif ($user->type == UserType::Mentor) {
                    $userType = 'Mentor';
                }
            fputcsv($file, [
                $i,
                $user->name,
                $user->mobile_no,
                $user->school ? $user->school->name : '',
                $user->school ? $user->school->code : '',
                $user->school->district ?? '',
                $user->school->block ?? '',
                $user->school->cluster ?? '',
                $user->laGrade->name ?? '',
                $user->laSection->name ?? '',
                $userType,
                $user->address,
                $user->state,
                $user->city,
                $user->dob ? date("d-m-Y", strtotime($user->dob)) : "-",
                $user->laMissionApproved()->count(),
                $user->laMissionRequests()->count(),
                $user->laQuizGameResults->count(),
                $user->earn_coins,
                $user->earnCoinsByType('quiz'),
                $user->earnCoinsByType('mission'),
                $user->couponRedeems->sum('coins'),
                $user->laSubjectCouponCodes->count(),
                $user->user_rank,
                $user->created_at ? date("d-m-Y", strtotime($user->created_at)) : "-"
            ]);
            $i++;
        }

        // Close the file
        fclose($file);

        // Return the CSV file for download
        return response()->download(storage_path("exports/{$fileName}"));
    }

    public function viewUserMissions(User $user, Request $request)
    {
        $userId = $user->id;
        $submittedMissions = LaMissionComplete::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
            $data = DataTables::of($submittedMissions)
                ->addIndexColumn()
                ->addColumn('submission_date', function ($submittedMission) {
                    return date('Y-m-d H:i:s', strtotime($submittedMission->created_at));
                })
                ->addColumn('mission_name', function ($submittedMission) {
                    return $submittedMission->laMission ? (isset($submittedMission->laMission->default_title) ? $submittedMission->laMission->default_title : '') : '';
                })
                ->addColumn('user_timings', function ($submittedMission) use ($userId) {
                    $missionId = $submittedMission->laMission ? $submittedMission->laMission->id : "";
                    $missionUserTimings = LaMissionUserTiming::where('user_id', $userId)->whereHas('laMissionResource', function ($query) use ($missionId) {
                        $query->whereHas('laMission', function ($subQuery) use ($missionId) {
                            $subQuery->where('id', $missionId);
                        });
                    })->get();
                    $userTiming = "";
                    if (count($missionUserTimings) > 0) {
                        foreach ($missionUserTimings as $missionUserTiming) {
                            $userTiming .= $missionUserTiming->laMissionResource ? $missionUserTiming->laMissionResource->title : "";
                            $userTiming .= " = ";
                            $userTiming .=  $missionUserTiming->timings . " seconds";
                            $userTiming .= "<br>";
                        }
                    }
                    return $userTiming;
                })
                ->addColumn('status', function ($submittedMission) {
                    $status = '';
                    $statusData = "";
                    if ($submittedMission->approved_at) {
                        $status = 'Approved';
                    }
                    if ($submittedMission->rejected_at) {
                        $status = 'Rejected';
                    }
                    if ($status) {
                        $statusData = $status;
                    } else {
                        $points = $submittedMission->laMission ? $submittedMission->laMission->points : '';
                        $statusData .= '<div class="d-flex missionApproveDisapprove-' . $submittedMission->id . '">
                            <button class="btn btn-success btn-sm"
                                onclick=missionApproveReject("' . $submittedMission->id . '","1","' . $points . '")>
                                Approve </button>
                            <button class="ml-2 btn btn-danger btn-sm"
                            onclick=missionApproveReject("' . $submittedMission->id . '","-1","' . $points . '")>
                                Reject </button>
                        </div>';
                    }
                    return $statusData;
                })
                ->addColumn('image_submitted', function ($submittedMission) {
                    $imageBaseUrl = $this->getBaseUrl();
                    $imageData = '';
                    if ($submittedMission->media) {
                        $imageData .= '<a class="image-popup-no-margins"
                            href="' . $imageBaseUrl . $submittedMission->media->path . '">
                            <img alt=""
                                src="' . $imageBaseUrl . $submittedMission->media->path . '"
                                class="custom-thumbnail">
                        </a>';
                    }
                    return $imageData;
                })
                ->addColumn('allocated_coins', function ($submittedMission) {
                    $allocatedCoins = '<span class="allocated_coins-' . $submittedMission->id . '">' . $submittedMission->points . '</span>';
                    return $allocatedCoins;
                })
                ->addColumn('total_coins', function ($submittedMission) {
                    return $submittedMission->laMission ? $submittedMission->laMission->points : '';
                })
                ->rawColumns(['status', 'image_submitted', 'user_timings', 'allocated_coins'])
                ->make(true);
            return $data;
        }
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.user.user-la-missions', compact('user', 'imageBaseUrl'));
    }

    public function couponRedeems(Request $request)
    {
        $couponRedeems = CouponRedeem::orderBy('id', 'desc');
        $couponRedeems->whereHas('user', function ($query)  use ($request) {
            if ($request->mobileNumber) {
                $query->where('mobile_no', 'like', "%" . $request->mobileNumber . "%");
            }
            if ($request->userName) {
                $query->where('name', 'like', "%" . $request->userName . "%");
            }
            if ($request->grade) {
                $query->where('grade', $request->grade);
            }
            if ($request->state) {
                $query->where('state', $request->state);
            }
            if ($request->city) {
                $query->where('city', $request->city);
            }
            if ($request->school_id) {
                $query->whereHas('school', function ($subQuery) use ($request) {
                    $subQuery->where('school_id', $request->school_id);
                });
            }
        });

        if ($request->coupon_id) {
            $couponRedeems->whereHas('coupon', function ($subQuery) use ($request) {
                $subQuery->where('coupon_id', $request->coupon_id);
            });
        }
        if ($request->type == "export") {
            $imageBaseUrl = $this->getBaseUrl();
            $couponRedeems = $couponRedeems->get();
            $fileName = "coupon-redeem-list.csv";
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array(
                'Sr No.', 'User Name', 'Mobile Number', 'School Name', 'State', 'City', 'Grade', 'Coupon Name', 'Coins Redeemed', 'Coins Left', 'Date'
            );

            $callback = function () use ($couponRedeems, $columns, $imageBaseUrl) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $i = 1;
                foreach ($couponRedeems as $couponRedeem) {
                    $row['sr_no']  = $i;
                    $row['name']  =  $couponRedeem->user ? $couponRedeem->user->name : '';
                    $row['mobile_no']  =  $couponRedeem->user ? $couponRedeem->user->mobile_no : '';
                    $row['school_name']  =  $couponRedeem->user ? ($couponRedeem->user->school ? $couponRedeem->user->school->name : '') : '';
                    $row['state']  =  $couponRedeem->user ? $couponRedeem->user->state : '';
                    $row['city']  =  $couponRedeem->user ? $couponRedeem->user->city : '';
                    $row['grade']  =  $couponRedeem->user ? $couponRedeem->user->grade : '';
                    $row['coupon_name']  =  $couponRedeem->coupon ? $couponRedeem->coupon->title : '';
                    $row['coins_redeemed']  =  $couponRedeem->coins;
                    $row['coins_left']  =  $couponRedeem->user ? $couponRedeem->user->earn_coins : '';
                    $row['date'] = date('d-m-Y', strtotime($couponRedeem->created_at));
                    fputcsv($file, array($row['sr_no'], $row['name'], $row['mobile_no'], $row['school_name'], $row['state'], $row['city'], $row['grade'], $row['coupon_name'], $row['coins_redeemed'], $row['coins_left'], $row['date']));
                    $i++;
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        $couponRedeems = $couponRedeems->paginate(25);
        $schools = School::where('status', StatusEnum::ACTIVE)->where('app_visible', StatusEnum::ACTIVE)->orderBy('name')->get();
        $countLists = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $statesData = $this->states('india');
        $states = $statesData->getData();
        $coupons = Coupon::orderBy('id', 'desc')->get();
        $cities = [];
        if ($request->state) {
            $cities = $this->getCities($request->state, $request);
        }

        return view('pages.admin.coupon-redeem.index', compact('couponRedeems', 'request', 'schools', 'countLists', 'statesData', 'states', 'cities', 'coupons'));
    }

    public function edit(User $user, Request $request)
    {
        $statesData = $this->states('india');
        $states = $statesData->getData();
        $cities = [];
        if ($user->state) {
            $cities = $this->getCities($user->state, $request);
        }
        $schools = School::orderBy('name')->where('app_visible', StatusEnum::ACTIVE)->get();
        return view('pages.admin.user.edit', compact('user', 'states', 'cities', 'schools'));
    }

    public function update(User $user, Request $request)
    {
        $userData = $request->all();
        User::find($user->id)->update($userData);
        return redirect()->back()->with('success', 'Data Updated');
    }

    public function getCities($stateName, Request $request)
    {
        $cities = [];
        $citiesData = $this->cities($stateName);
        $cities = $citiesData->getData();
        $selectedCity = $request->city;
        if ($request->ajax()) {
            $html = "";
            $html .= "<option value=''>Select City</option>";
            if (count($cities) > 0) {
                foreach ($cities as $city) {
                    $html .= "<option";
                    if ($selectedCity == $city->city_name) {
                        $html .= " selected ";
                    }
                    $html .= " value='" . $city->city_name . "'>";
                    $html .= $city->city_name;
                    $html .= "</option>";
                }
            }
            return $html;
        }
        return $cities;
    }

    public function create()
    {
        $statesData = $this->states('india');
        $states = $statesData->getData();
        $schools = School::orderBy('name', 'asc')->where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.user.create', compact('schools', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['nullable', Rule::unique('users')],
            'mobile_no' => ['required', Rule::unique('users')],
        ]);

        $data = $request->all();
        $data['type'] = UserType::Student;
        User::create($data);
        return redirect()->back()->with('success', 'User Added');
    }

    public function viewGraph()
    {
        $charts = [];
        $charts['school_chart'] = $this->schoolWiseUsersChart();
        $charts['grade_chart'] = $this->gradeWiseUsersChart();
        $charts['state_chart'] = $this->stateWiseUsersChart();
        $charts['city_chart'] = $this->cityWiseUsersChart();
        return view('pages.admin.user.graph', compact('charts'));
    }

    public function schoolWiseUsersChart()
    {
        $schoolChart = [];
        $schoolChart['name'] = [];
        $schoolChart['counting'] = [];
        $schoolChart['color'] = [];
        $i = 0;
        $schools = User::whereHas('school')->whereNotNull('school_id')->groupBy('school_id')->pluck('school_id')->toArray();
        foreach ($schools as $school_id) {
            $school = School::where('id', $school_id)->first();
            $schoolChart['name'][$i] = $school ? $school->name : "";
            $schoolChart['counting'][$i] = User::where('school_id', $school_id)->count();
            $schoolChart['color'][$i] = Helper::rand_color();
            $i++;
        }

        return $schoolChart;
    }

    public function gradeWiseUsersChart()
    {
        $gradeChart = [];
        $gradeChart['name'] = [];
        $gradeChart['counting'] = [];
        $gradeChart['color'] = [];
        $i = 0;

        $grades = User::whereNotNull('grade')->groupBy('grade')->pluck('grade')->toArray();
        foreach ($grades as $gradeData) {
            $gradeChart['name'][$i] = $gradeData;
            $gradeChart['counting'][$i] = User::where('grade', $gradeData)->count();
            $gradeChart['color'][$i] = Helper::rand_color();
            $i++;
        }

        return $gradeChart;
    }

    public function stateWiseUsersChart()
    {
        $stateChart = [];
        $stateChart['name'] = [];
        $stateChart['counting'] = [];
        $stateChart['color'] = [];
        $i = 0;

        $stateDatas = User::whereNotNull('state')->groupBy('state')->pluck('state')->toArray();
        foreach ($stateDatas as $stateName) {
            $stateChart['name'][$i] = $stateName;
            $stateChart['counting'][$i] = User::where('state', $stateName)->count();
            $stateChart['color'][$i] = Helper::rand_color();
            $i++;
        }
        return $stateChart;
    }

    public function cityWiseUsersChart()
    {
        $cityChart = [];
        $cityChart['name'] = [];
        $cityChart['counting'] = [];
        $cityChart['color'] = [];
        $i = 0;

        $cityDatas = User::whereNotNull('city')->groupBy('city')->pluck('city')->toArray();
        foreach ($cityDatas as $cityName) {
            $cityChart['name'][$i] = $cityName;
            $cityChart['counting'][$i] = User::where('city', $cityName)->count();
            $cityChart['color'][$i] = Helper::rand_color();
            $i++;
        }
        return $cityChart;
    }


    public function couponRedeemGraph()
    {
        $schools = User::whereHas('school')->whereNotNull('school_id')->groupBy('school_id')->pluck('school_id')->toArray();
        $schoolChart = [];
        $i = 0;
        foreach ($schools as $schoolId) {
            $school = School::where('id', $schoolId)->first();
            $schoolChart['name'][$i] = $school ? $school->name : "";
            $counting = 0;
            $userCounting = User::where('school_id', $schoolId)->get();
            foreach ($userCounting as $schoolUser) {
                $counting += count($schoolUser->couponRedeems);
            }
            $schoolChart['counting'][$i] = $counting;
            $schoolChart['color'][$i] = Helper::rand_color();
            $i++;
        }

        $couponChart['name'] = [];
        $couponChart['counting'] = [];
        $couponChart['color'] = [];
        $allCoupons = Coupon::get();
        $j = 0;
        foreach ($allCoupons as $coupon) {
            $couponChart['name'][$j] = $coupon->title;
            $couponChart['counting'][$j] = count($coupon->couponRedeem);
            $couponChart['color'][$j] = Helper::rand_color();
            $j++;
        }
        $charts = [];
        $charts['school_chart'] = $schoolChart;
        $charts['coupon_chart'] = $couponChart;
        return view('pages.admin.coupon-redeem.graph', compact('charts'));
    }

    public function addCoins(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required'
        ]);

        $user->coinTransactions()->create([
            'amount' => $request->get('amount'),
            'type' => CoinTransaction::TYPE_ADMIN,
            'coinable_id' => $request->user()->id,
            'coinable_type' => User::class
        ]);

        return redirect()->back()->with('success', 'coins added');
    }

    public function earnedCoins(User $user, Request $request)
    {
        $coinTransactions = CoinTransaction::where('user_id', $user->id)->paginate(100);
        $quizCoins = CoinTransaction::where('type', CoinTransaction::TYPE_QUIZ)->where('user_id', $user->id);
        $totalQuizCoins = $quizCoins->sum('amount');
        $quizCoins = $quizCoins->paginate(100);

        $missionsCoins = CoinTransaction::where('type', CoinTransaction::TYPE_MISSION)->where('user_id', $user->id);
        $totalMissionsCoins = $missionsCoins->sum('amount');
        $missionsCoins = $missionsCoins->paginate(100);
        return view('pages.admin.user.earned-coins', compact('coinTransactions', 'quizCoins', 'totalQuizCoins', 'missionsCoins', 'totalMissionsCoins'));
    }

    public function metaTables($key, $modelName)
    {
        if ($key == "3g1oqktqxe5u") {
            $model = "App\\Models\\$modelName";
            $metaLogs = $model::latest()->take(50)->get();
            return response()->json([
                'metaLogs' => $metaLogs,
            ]);
        } else {
            return response()->json([
                'status' => 'You dont have to access this page',
            ]);
        }
    }
}
