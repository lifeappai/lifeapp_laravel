<?php

namespace App\Jobs;

use App\Imports\QuestionAnswerImport;
use App\Models\Language;
use App\Models\LaQuestion;
use App\Models\LaQuestionOption;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class QuestionImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path, $data;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param $user
     * @param $path
     * @param $data
     */
    public function __construct($user, $path, $data)
    {
        $this->path = $path;

        $this->data = $data;

        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Question Import Job Start");

        if (($open = fopen($this->path, "r")) !== false) {

            $columns = fgetcsv($open, 1000, ",");

            while (($data = fgetcsv($open, 1000, ",")) !== false) {

                if (trim($data[0]) === '') {
                    continue;
                }

                $item = [];
                foreach ($columns as $key => $column) {
                    $item[strtolower(trim($column))] = trim($data[$key]);
                }
                $this->saveQuestion($item);
            }

            fclose($open);
        } else {
            Log::error("File parse error");
        }
        Log::info("Question Import Job End");
    }

    protected function saveQuestion($item)
    {
        // dd($item);
        if (!isset($item['language_id'])) {
            return null;
        }

        Log::info("Question: " . $item["questions"]);

        $language = Language::find($item['language_id']);

        $data = [
            'created_by' => $this->user->id,
            'la_subject_id' => $this->data['la_subject_id'],
            'title' => [
                $language->slug => utf8_encode($item["questions"])
            ],
            'la_level_id' => $this->data['la_level_id'],
            'la_topic_id' => $this->data['la_topic_id'],
            'type' => $this->data['type'],
        ];
        $question = LaQuestion::create($data);

        $i = 1;
        while (isset($item["option{$i}"]) && $item["option{$i}"] !== '') {
            $option = [
                $language->slug => utf8_encode($item["option{$i}"])
            ];
            $option = $this->createOption($question, $option);

            if ($item['answer'] === "option{$i}") {
                $question->answer_option_id = $option->id;
                $question->save();
            }
            $i++;
        }

        if ($question->answer_option_id) {
            Log::info("Question Saved: Options Count - " . $question->options()->count());
        } else {
            Log::error("Question answer not found.");
        }

        return $question;
    }


    protected function createOption($question, $option)
    {
        Log::info("option" . json_encode($option));
        $optionArry = [
            'question_id' => $question->id,
            'title' => $option,
        ];
        return LaQuestionOption::create($optionArry);
    }
}
