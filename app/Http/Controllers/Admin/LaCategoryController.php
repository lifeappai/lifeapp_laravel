<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GameType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaMission;
use App\Models\LaQuestion;
use Illuminate\Http\Request;

class LaCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories['missions'] = LaMission::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::MISSION)->count();
        $categories['quizes'] = LaQuestion::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::QUIZ)->count();
        $categories['riddles'] = LaQuestion::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::RIDDLE)->count();
        $categories['puzzles'] = LaQuestion::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::PUZZLE)->count();
        $categories['jigyasas'] = LaMission::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::JIGYASA)->count();
        $categories['pragyas'] = LaMission::where('status', StatusEnum::ACTIVE)->where('la_subject_id', $request->la_subject_id)->where('la_level_id', $request->la_level_id)->where('type', GameType::PRAGYA)->count();
        return view('pages.admin.categories.index', compact('categories'));
    }
}
