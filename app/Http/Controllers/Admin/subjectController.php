<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LanguageEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\Language;
use App\Models\LaSubject;
use App\Models\LaSubjectCouponCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class subjectController extends Controller
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
        $subjects = LaSubject::orderBy('index')->get();
        return view('pages.admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.subjects.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $titleData = [];
        $headingData = [];
        $image = [];
        foreach ($request->subject_translation as $key => $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $headingData[$cn['language']] = $cn['heading'];
            $media = $this->upload($cn['image']);
            $image[$cn['language']] = $media->id;
        }
        $data = $request->all();
        $data['title'] = $titleData;
        $data['heading'] = $headingData;
        $data['created_by'] = Auth::user()->id;
        $data['image'] = $image;
        $data['coupon_code_count'] = 0;
        $LaSubjects = LaSubject::create($data);
        $this->generateCouponCodes($LaSubjects, $request);
        return redirect()->route('admin.subjects.index')->with('success', 'Subject Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaSubject $subject)
    {
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.subjects.edit', compact('languages', 'subject', 'imageBaseUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaSubject $subject)
    {
        $data = $request->except(['_method', '_token']);
        $titleData = [];
        $headingData = [];
        $image = [];
        foreach ($request->subject_translation as $key => $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $headingData[$cn['language']] = $cn['heading'];
            $mediaId = null;
            if (isset($cn['media_id'])) {
                $mediaId = $cn['media_id'];
            }
            if (isset($cn['image'])) {
                $media = $this->upload($cn['image']);
                $mediaId = $media->id;
            }
            $image[$cn['language']] =  $mediaId;
        }
        $data['title'] = $titleData;
        $data['heading'] = $headingData;
        $data['image'] = $image;
        $data['created_by'] = Auth::user()->id;
        LaSubject::find($subject->id)->update($data);
        return redirect()->route('admin.subjects.index')->with('success', 'Subject Updated');
    }

    public function statusChange(LaSubject $subject)
    {
        if ($subject->status == StatusEnum::ACTIVE) {
            $subject->status = StatusEnum::DEACTIVE;
        } else {
            $subject->status =  StatusEnum::ACTIVE;
        }
        $subject->save();
        return response()->json(["status" => 200, "message" => "Subject Status Changed"]);
    }

    public function indexChange(LaSubject $subject, Request $request)
    {
        $index = $request->index;
        if (!$index) {
            $index = 1;
        }
        $subject->index = $index;
        $subject->update();
        return response()->json(["status" => 200, "message" => "Subject Index Changed", "index" => $index]);
    }

    public function getRandomString($length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public function couponCodes(Request $request)
    {
        $laSubjectId = $request->la_subject_id;
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();

        $laSubjectCouponCodes = LaSubjectCouponCode::orderBy('id', 'desc');
        if ($laSubjectId) {
            $laSubjectCouponCodes = $laSubjectCouponCodes->where('la_subject_id', $laSubjectId);
        }
        if ($request->type == "export") {
            $laSubjectCouponCodes = $laSubjectCouponCodes->get();
            $fileName = "subject-coupon-codes.csv";
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array(
                'Sr No.', 'Subject', 'Coupon Code', 'Assign Coupon Student', 'Unlock Coupon Date Time'
            );

            $callback = function () use ($laSubjectCouponCodes, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $i = 1;
                foreach ($laSubjectCouponCodes as $laSubjectCouponCode) {
                    $row['sr_no'] = $i;
                    $row['subject'] = $laSubjectCouponCode->laSubject ? $laSubjectCouponCode->laSubject->default_title : '';
                    $row['coupon_code'] = $laSubjectCouponCode->coupon_code;
                    $row['name'] = $laSubjectCouponCode->user ? $laSubjectCouponCode->user->name : '';
                    $row['unlock_coupon_at'] = $laSubjectCouponCode->unlock_coupon_at;
                    fputcsv($file, array($row['sr_no'], $row['subject'], $row['coupon_code'], $row['name'], $row['unlock_coupon_at']));
                    $i++;
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        $laSubjectCouponCodes = $laSubjectCouponCodes->paginate(25);
        return view('pages.admin.subjects.coupon-codes', compact('laSubjectCouponCodes', 'subjects', 'laSubjectId'));
    }

    public function generateCouponCodes(LaSubject $subject, Request $request)
    {
        $couponCodeId = 0;
        for ($i = 1; $i <= $request->coupon_code_count; $i++) {
            $checkPreviousCoupon = LaSubjectCouponCode::orderBy('id', 'desc')->first();
            if ($checkPreviousCoupon) {
                $couponCodeId = $checkPreviousCoupon->id;
            }
            LaSubjectCouponCode::create([
                "la_subject_id" => $subject->id,
                "coupon_code" => "SUB" . $couponCodeId . $this->getRandomString(7),
            ]);
        }
        $subject->coupon_code_count = $subject->coupon_code_count + $request->coupon_code_count;
        $subject->save();
        return [
            "status" => 200,
            "total_codes" => $subject->coupon_code_count,
            "subject_id" => $subject->id,
            "message" => $subject->default_title . "Coupon Code Added",
        ];
    }
}
