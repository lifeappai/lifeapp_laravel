<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Imports\QuestionAnswerImport;
use App\Jobs\QuestionImportJob;
use App\Models\LaLevel;
use App\Models\Language;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaImportQuestionController extends Controller
{
    public function index(Request $request)
    {
        $levels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $laTopics = LaTopic::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.question-import.index', compact('levels', 'subjects', 'languages', 'laTopics', 'request'));
    }

    public function import(Request $request)
    {
        $path = storage_path('csv/' . time() . '.csv');

        file_put_contents($path, file_get_contents($request->file('question_excel_sheet')));

        $data['la_subject_id'] = $request->la_subject_id;
        $data['la_level_id'] = $request->la_level_id;
        $data['la_topic_id'] = $request->la_topic_id;
        $data['type'] = $request->type;

        dispatch(new QuestionImportJob($request->user(), $path, $data));
        return redirect()->back()->with('success', 'Data Imported Successfully');
    }
}
