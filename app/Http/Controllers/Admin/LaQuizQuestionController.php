<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaQuiz;
use App\Models\LaQuizOption;
use App\Models\LaQuizQuestion;
use Illuminate\Http\Request;

class LaQuizQuestionController extends Controller
{
    public function create(Request $request, LaQuiz $laQuiz)
    {
        return view('');
    }

    public function store(Request $request, LaQuiz $laQuiz)
    {
        $data = $request->validate([
            'text' => 'required',
            'locale' => 'required|in:en,mr,hi',

            'options' => 'required|array|min:2'
        ]);

        $laQuestion = new LaQuizQuestion([
            'default_text' => $data['text'],
            'text' => [
                $data['locale'] => $data['text']
            ]
        ]);
        $laQuestion->created_by = $request->user()->id;
        $laQuestion->save();

        $options = $data['options'];

        foreach ($options as $option) {
            $laOption = new LaQuizOption([
                'default_text' => $option,
                "text" => [
                    $data['locale'] => $option
                ]
            ]);
            $laOption->la_quiz_question_id = $laQuestion->id;
            $laOption->save();
        }

        $laQuestion->load('options');

        return $laQuestion;
    }

    public function index(Request $request, LaQuiz $laQuiz)
    {
        $laQuestions = $laQuiz->questions()->with('options')->get();

        return $laQuestions;
    }

    public function storeTranslation(Request $request, LaQuiz $laQuiz, LaQuizQuestion $laQuizQuestion)
    {
        $data = $request->validate([
            'text' => 'required',
            'locale' => 'required|in:en,mr,hi',

            'options' => 'required|array|min:2'
        ]);

        $locale = $data['locale'];

        $text = $laQuizQuestion->text;
        $text[$locale] = $data['text'];
        $laQuizQuestion->text = $text;
        $laQuizQuestion->save();

        $options = $data['options'];
        $savedOptions = $laQuizQuestion->options()->get();

        for ($i = 0; ($i < $savedOptions && $i < count($options)); $i++) {
            $questionOption = $savedOptions[$i];
            $text = $questionOption->text;
            $text[$locale] = $options[$i];
            $questionOption->text = $text;
            $questionOption->save();
        }

        $laQuizQuestion->load('options');
        return $laQuizQuestion;
    }


}
