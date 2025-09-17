<?php

namespace App\Imports;

use App\Models\Language;
use App\Models\LaQuestion;
use App\Models\LaQuestionOption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class QuestionAnswerImport implements ToCollection
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $requestedData = $this->request;
        $langQuestion = [];
        $j = 0;
        foreach ($rows as $key => $row) {
            if ($key != 0) {
                if ($row[0] != '' || $row[1] != '') {
                    $langQuestion[$j][$key]['language'] = $row[0];
                    $langQuestion[$j][$key]['question'] = $row[1];
                    $langQuestion[$j][$key]['option1'] = $row[2];
                    $langQuestion[$j][$key]['option2'] = $row[3];
                    $langQuestion[$j][$key]['option3'] = $row[4];
                    $langQuestion[$j][$key]['option4'] = $row[5];
                    $langQuestion[$j][$key]['answer'] = $row[6];
                }

                if ($row[0] == '' && $row[1] == '' && $row[2] == '' && $row[3] == '' && $row[4] == '' && $row[5] == '' && $row[6] == '') {
                    $j++;
                }
            }
        }

        Log::info("start Add Question");
        foreach ($langQuestion as $key => $question) {
            $finalQuestion = [];
            $options = [];
            $answer = '';
            foreach ($question as $qu) {
                $languageCheck = Language::find($qu['language']);
                $finalQuestion[$languageCheck->slug] = $qu['question'];
                $options[1][$languageCheck->slug] = $qu['option1'];
                $options[2][$languageCheck->slug] = $qu['option2'];
                $options[3][$languageCheck->slug] = $qu['option3'];
                $options[4][$languageCheck->slug] = $qu['option4'];
                $answer = $qu['answer'] != '' ? $qu['answer'] : '';
            }

            $data = [
                'created_by' => Auth::id(),
                'la_subject_id' => $requestedData['la_subject_id'],
                'title' => $finalQuestion,
                'la_level_id' => $requestedData['la_level_id'],
            ];
            $question = LaQuestion::create($data);

            $optionKey = 0;
            $questionOptionsResult = [];

            foreach ($options as $option) {
                $optionArry = [
                    'question_id' => $question->id,
                    'title' => $option,
                ];
                $laQAOption = LaQuestionOption::create($optionArry);

                $questionOptionsResult[$optionKey] = $laQAOption->id;
                $optionKey++;
            }

            $question->answer_option_id = $answer == 'option1' ? $questionOptionsResult[0] : ($answer == 'option2' ? $questionOptionsResult[1] : ($answer == 'option3' ? $questionOptionsResult[2] : ($answer == 'option4' ? $questionOptionsResult[3] : null)));
            $question->update();
            Log::info("Data add: " . $key . " Option Id: " . $question->answer_option_id);
        }
    }
}
