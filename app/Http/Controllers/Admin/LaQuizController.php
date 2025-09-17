<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaQuestion;
use App\Models\LaQuestionOption;
use App\Models\LaQuiz;
use App\Models\LaQuizGameQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaQuizController extends Controller
{
    public function create(Request $request)
    {
        return view('');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'level' => 'required|in:0,1,2,3',
            'coins' => 'required|min:0'
        ]);

        $laQuiz = new LaQuiz($data);
        $laQuiz->created_by = $request->user()->id;
        $laQuiz->save();

        return $laQuiz;
    }

    public function index(Request $request)
    {
        $laQuizzes = LaQuiz::latest()->get();
        return $laQuizzes;
    }

    public function edit(Request $request, LaQuiz $laQuiz)
    {
        return $laQuiz;
    }

    public function update(Request $request, LaQuiz $laQuiz)
    {
        $data = $request->validate([
            'level' => 'required|in:0,1,2,3'
        ]);

        $laQuiz->update($data);
        return $laQuiz;
    }

    public function delete(Request $request, LaQuiz $laQuiz)
    {
        $laQuiz->delete();

        return view('');
    }
}
