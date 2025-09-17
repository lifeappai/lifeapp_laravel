<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Enums\SubjectEnum;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\CoinTransaction;
use App\Models\LaLevel;
use App\Models\LaMission;
use App\Models\LaMissionComplete;
use App\Models\LaMissionResource;
use App\Models\Language;
use App\Models\LaQuestion;
use App\Models\LaSubject;
use App\Models\LaTopic;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Yajra\DataTables\Facades\DataTables;

class LaMissionController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $questions = LaQuestion::orderBy('index')->get();
        $subjectId = $request->la_subject_id;
        $levelId = $request->la_level_id;
        $statusId = $request->status;
        $type = $request->type;
        $missions = LaMission::orderBy('index');
        if ($subjectId) {
            $missions->where('la_subject_id', $subjectId);
        }
        if ($levelId) {
            $missions->where('la_level_id', $levelId);
        }
        if ($type) {
            $missions->where('type', $type);
        }
        if (isset($statusId)) {
            $missions->where('status', $statusId);
        }
        $missions = $missions->paginate(25);
        return view('pages.admin.la-missions.index', compact('missions', 'subjects', 'questions', 'subjectId', 'statusId', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.la-missions.create', compact('languages', 'subjects', 'laLevels', 'request'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $description = [];
        $title = [];
        $image = [];
        $document = [];
        $question = [];
        foreach ($request->mission as $key => $cn) {
            $description[$cn['language']] = $cn['description'];
            $title[$cn['language']] = $cn['title'];
            $question[$cn['language']] = $cn['question'];
            $media = $this->upload($cn['image']);
            $image[$cn['language']] =  $media->id;
            if (isset($cn['document'])) {
                $documentMedia = $this->upload($cn['document']);
                $document[$cn['language']] =  $documentMedia->id;
            }
        }
        $data = $request->all();
        $data['title'] = $title;
        $data['description'] = $description;
        $data['image'] = $image;
        $data['question'] = $question;
        $data['document'] = $document;
        LaMission::create($data);
        $inputs = $request->except('_token', 'allow_for', 'mission');
        return redirect()->route('admin.la.missions.index', $inputs)->with('success', 'Mission Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaMission $laMission)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.la-missions.edit', compact('languages', 'laMission', 'subjects', 'imageBaseUrl', 'laLevels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaMission $laMission)
    {
        $data = $request->except(['_method', '_token']);
        $description = [];
        $title = [];
        $image = [];
        $document = [];
        $question = [];
        foreach ($request->mission as $key => $cn) {
            $description[$cn['language']] = $cn['description'];
            $title[$cn['language']] = $cn['title'];
            $question[$cn['language']] = $cn['question'];
            $mediaId = null;
            if (isset($cn['media_id'])) {
                $mediaId = $cn['media_id'];
            }
            if (isset($cn['image'])) {
                $media = $this->upload($cn['image']);
                $mediaId = $media->id;
            }
            $image[$cn['language']] =  $mediaId;
            $documentId = null;
            if (isset($cn['document_id'])) {
                $documentId = $cn['document_id'];
                $document[$cn['language']] =  $documentId;
            }
            if (isset($cn['document'])) {
                $documentMedia = $this->upload($cn['document']);
                $documentId = $documentMedia->id;
                $document[$cn['language']] =  $documentId;
            }
        }
        $data = $request->all();
        $data['title'] = $title;
        $data['description'] = $description;
        $data['image'] = $image;
        $data['document'] = $document;
        $data['question'] = $question;
        LaMission::find($laMission->id)->update($data);
        $inputs = $request->except('_token', 'allow_for', 'mission');
        return redirect()->route('admin.la.missions.index', $inputs)->with('success', 'Mission Updated');
    }

    public function statusChange(LaMission $laMission)
    {
        if ($laMission->status == StatusEnum::ACTIVE) {
            $laMission->status = StatusEnum::DEACTIVE;
        } else {
            $laMission->status =  StatusEnum::ACTIVE;
        }
        $laMission->update();
        return response()->json(["status" => 200, "message" => "Mission Status Changed"]);
    }

    public function indexChange(LaMission $laMission, Request $request)
    {
        $index = $request->index;
        if (!$index) {
            $index = 1;
        }
        $laMission->index = $index;
        $laMission->update();
        return response()->json(["status" => 200, "message" => "Mission Index Changed", "index" => $index]);
    }

    public function editResources(LaMission $laMission)
    {
        $resources = LaMissionResource::where('la_mission_id', $laMission->id)->groupBy('index')->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.la-missions.resources', compact('laMission', 'languages', 'resources', 'imageBaseUrl'));
    }

    public function updateResources(LaMission $laMission, Request $request)
    {
        $laMission->laMissionResources()->delete();
        $i = 1;
        foreach ($request->new_resources as $cn) {
            foreach ($cn['resource'] as $resource) {
                $mediaId = null;
                if (isset($resource['media_id'])) {
                    $mediaId = $resource['media_id'];
                }
                if (isset($resource['image'])) {
                    $missionResourceMedia = $this->upload($resource['image']);
                    $mediaId = $missionResourceMedia->id;
                }
                $resourceData = [
                    "locale" => $resource['language'],
                    "title" => $resource['title'],
                    "la_mission_id" => $laMission->id,
                    "media_id" => $mediaId,
                    "index" => $i,
                ];
                LaMissionResource::create($resourceData);
            }
            $i++;
        }
        return redirect()->route('admin.la.missions.index')->with('success', 'Resources Added');
    }

    public function missionSubmissions(Request $request)
    {
        $date = $request->from_date;
        $end_date = $request->end_date;
        $missionType = $request->missionType;
        $assignedBy = $request->assignedBy;
        // Query with join to fetch teacher's name
        $submittedMissions = LaMissionComplete::with(['user.school', 'laMission.subject', 'media'])
            ->leftJoin('la_mission_assigns', function ($join) {
                $join->on('la_mission_completes.la_mission_id', '=', 'la_mission_assigns.la_mission_id')
                    ->on('la_mission_completes.user_id', '=', 'la_mission_assigns.user_id');
            })
            ->leftJoin('users as teachers', 'la_mission_assigns.teacher_id', '=', 'teachers.id')
            ->select('la_mission_completes.*', 'teachers.name as teacher_name')
            ->orderBy('la_mission_completes.created_at', 'desc');

        if ($date) {
            $submittedMissions = $submittedMissions->where('la_mission_completes.created_at', '>=', $date);
        }
        if ($end_date) {
            $submittedMissions = $submittedMissions->whereDate('la_mission_completes.created_at', '<=', $end_date);
        }

        if ($missionType == "approved") {
            $submittedMissions = $submittedMissions->whereNotNull('la_mission_completes.approved_at');
        } else if ($missionType == "requested") {
            $submittedMissions = $submittedMissions->whereNull('la_mission_completes.approved_at')
                ->whereNull('la_mission_completes.rejected_at');
        } else if ($missionType == "rejected") {
            $submittedMissions = $submittedMissions->whereNotNull('la_mission_completes.rejected_at');
        }

        if ($assignedBy == 'teacher') {
            $submittedMissions = $submittedMissions->whereNotNull('teachers.id');
        } elseif ($assignedBy == 'self') {
            $submittedMissions = $submittedMissions->whereNull('teachers.id');
        }

        $imageBaseUrl = $this->getBaseUrl();

        if ($request->type == "export") {
            $submittedMissions = $submittedMissions->get();

            $fileName = "missions.csv";
            $headers = array(
                "Content-type"        => "text/csv, charset=utf-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0",
            );

            $columns = array(
                'Sr No.', 'Student Name', 'Mobile Number', 'School Name', 'District Name', 'Block Name', 'Cluster Name', 'State', 'City', 'Grade', 'Submission Date', 'Mission Name', "Assigned By", 'Submitted Image', "Allocated Coins", "Total Coins", "Subject", "Status"
            );

            $callback = function () use ($submittedMissions, $columns, $imageBaseUrl) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $i = 1;
                foreach ($submittedMissions as $submittedMission) {
                    $row['sr_no']  = $i;
                    $row['student_name']  =  $submittedMission->user->name ?? '';
                    $row['mobile_number']  =  $submittedMission->user->mobile_no ?? '';
                    $row['school_name']  = $submittedMission->user->school->name ?? '';
                    $row['district_name']  =  $submittedMission->user->school->district ?? '';
                    $row['block_name']  =  $submittedMission->user->school->block ?? '';
                    $row['cluster_name']  = $submittedMission->user->school->cluster ?? '';
                    $row['state']  =  $submittedMission->user->state ?? '';
                    $row['city']  =  $submittedMission->user->city ?? '';
                    $row['grade']  =  $submittedMission->user->grade ?? '';
                    $row['submission_date']  = date('Y-m-d H:i:s', strtotime($submittedMission->created_at));
                    $row['mission_name']  = $submittedMission->laMission ? (isset($submittedMission->laMission->default_title) ? $submittedMission->laMission->default_title : '') : '';
                    $row['assigned_by'] = $submittedMission->teacher_name ?? 'Self';
                    $row['image_submitted']  = $submittedMission->media ? $imageBaseUrl . $submittedMission->media->path : "";
                    $row['allocated_coins']  = $submittedMission->laMission ? $submittedMission->laMission->points : '';
                    $row['total_coins']  = $submittedMission->points;
                    $row['subject']  = $submittedMission->laMission ? ($submittedMission->laMission->subject ? $submittedMission->laMission->subject->default_title : '') : '';
                    $row['status'] = ($submittedMission->approved_at == "" && $submittedMission->rejected_at == "") ? "Pending" : (($submittedMission->approved_at != "") ? "Approved" : "Rejected");
                    fputcsv($file, array($row['sr_no'], $row['student_name'], $row['mobile_number'], $row['school_name'], $row['district_name'], $row['block_name'], $row['cluster_name'], $row['state'], $row['city'], $row['grade'], $row['submission_date'], $row['mission_name'], $row['assigned_by'], $row['image_submitted'], $row['allocated_coins'], $row['total_coins'], $row['subject'], $row['status']));
                    $i++;
                }

                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        $submittedMissions = $submittedMissions->paginate(25);
        return view('pages.admin.la-missions.submissions', compact('submittedMissions', 'date', 'missionType', 'end_date', 'imageBaseUrl'));
    }

    public function missionSubmissionsChart()
    {
        $charts = [
            'school_chart' => $this->missionCompletedSchools(),
            'state_chart' => $this->missionCompletedStateWise(),
            'grade_chart' => $this->missionCompletedGradesWise(),
            'mission_chart' => $this->missionCompletedMissionDetails(),
        ];

        return view('pages.admin.la-missions.submissions-chart', compact('charts'));
    }
    // completed mission details on grade wise
    public function missionCompletedStateWise()
    {
        $userStates = User::whereNotNull('state')->groupBy('state')->pluck('state')->toArray();
        $stateChart['name'] = [];
        $stateChart['counting'] = [];
        $stateChart['color'] = [];
        $i = 0;
        foreach ($userStates as $state) {
            $stateChart['name'][$i] = $state;
            $stateChart['counting'][$i] = User::where('state', $state)->count();
            $stateChart['color'][$i] = Helper::rand_color();
            $i++;
        }
        return $stateChart;
    }
    // completed mission details on grade wise
    public function missionCompletedGradesWise()
    {
        $userGrades = User::whereNotNull('grade')->groupBy('grade')->pluck('grade')->toArray();
        $gradeChart['name'] = [];
        $gradeChart['counting'] = [];
        $gradeChart['color'] = [];
        $i = 0;
        foreach ($userGrades as $grade) {
            $gradeChart['name'][$i] = $grade;
            $gradeChart['counting'][$i] = User::where('grade', $grade)->count();
            $gradeChart['color'][$i] = Helper::rand_color();
            $i++;
        }
        return $gradeChart;
    }

    // completed mission details on mission wise
    public function missionCompletedMissionDetails()
    {
        $laMissions = LaMission::where('status', StatusEnum::ACTIVE)->get();
        $missionChart['name'] = [];
        $missionChart['counting'] = [];
        $missionChart['color'] = [];
        $i = 0;
        foreach ($laMissions as $mission) {
            $missionChart['name'][$i] = $mission->default_title;
            $missionChart['counting'][$i] = count($mission->missionCompletes);
            $missionChart['color'][$i] = Helper::rand_color();
            $missionChart['id'][$i] = $mission->id;
            $i++;
        }
        return $missionChart;
    }
    // completed mission details on school wise
    public function missionCompletedSchools()
    {

        $allSchools = School::where('status', StatusEnum::ACTIVE)->where('app_visible', StatusEnum::ACTIVE)->get();
        $schoolChart['name'] = [];
        $schoolChart['counting'] = [];
        $schoolChart['color'] = [];
        $i = 0;
        foreach ($allSchools as $school) {
            if (count($school->users) > 0) {
                $schoolChart['name'][$i] = $school->name;
                $counting = 0;
                foreach ($school->users as $schoolUser) {
                    $counting += count($schoolUser->laMissionCompletes);
                }
                $schoolChart['counting'][$i] = $counting;
                $schoolChart['color'][$i] = Helper::rand_color();
                $i++;
            } else {
                $schoolChart['name'][$i] = $school->name;
                $schoolChart['counting'][$i] = 0;
                $schoolChart['color'][$i] = Helper::rand_color();
                $i++;
            }
        }
        return $schoolChart;
    }

    public function approveRejectUserMission(LaMissionComplete $laMissionComplete, Request $request)
    {
        $data = $request->all();
        $comment = isset($data['comment']) ? $data['comment'] : null;
        $mission = $laMissionComplete->laMission;
        if (!$mission) {
            return [
                "status" => 400,
                "message" => "Mission Not Added",
            ];
        }

        $points = 0;
        if (isset($data['points'])) {
            $points = $data['points'];
            $laMissionComplete->points = $data['points'];
        }

        $laMissionComplete->save();
        $user = $laMissionComplete->user;

        $message = "Mission Rejected";
        $missionStatus = "Rejected";
        if ($data['status'] == 1) {
            $laMissionComplete->approved($comment);
            $user->createTransaction($laMissionComplete, $points, CoinTransaction::TYPE_MISSION);
            $mission->sendApproveNotification($user);
            $message = "Mission Approved";
            $missionStatus = "Approved";
        } else {
            $laMissionComplete->rejected($comment);
            $mission->sendRejectNotification($user);
        }
        return [
            "status" => 200,
            "message" => $message,
            "submit_mission_id" => $laMissionComplete->id,
            "mission_status" => $missionStatus,
            "allocated_coins" => $laMissionComplete->points,
        ];
    }
}
