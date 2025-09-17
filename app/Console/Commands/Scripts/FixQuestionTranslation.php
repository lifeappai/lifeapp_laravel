<?php

namespace App\Console\Commands\Scripts;

use App\Models\LaQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FixQuestionTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:questions {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $total = 0;
        LaQuestion::orderBy('id', 'desc')->where("id", "<" ,247)->with('options')->chunk(50, function ($questions) use ($total) {
            $total += $questions->count();
            $this->line("{$questions->count()} - {$total}");

            foreach ($questions as $question) {
                $question->title = $this->fixTranslations($question->title);
                $question->save();
                $pData = [];
                $pData["text"] = $question->title["en"];
                $pData["options"] = [];
                $pData["subject1"] = $question->subject->title["en"] ?? null;

                foreach ($question->options as $opt) {
                    $opt->title = $this->fixTranslations($opt->title);
                    $opt->save();
                    $pData["options"][] = ["text" => $opt->title["en"]];

                    if ($question->answer_option_id == $opt->id) {
                        $pData["answer"] = $opt->title["en"];
                    }
                }

                $this->line("Post Data" . json_encode($pData));

                $this->postData($pData);
            }
            return true;
        });

        return 0;
    }

    protected function fixTranslations($questionTitles)
    {
        $titles = [];
        foreach ($questionTitles as $key => $text) {
            if (in_array($key, ["hi", "en", "mr"])) {
                $titles[$key] = trim($text);
            } else {
                $titles["en"] = trim($text);
            }
        }
        return $titles;
    }

    public function postData($data)
    {
        $response = Http::acceptJson()
            ->contentType("application/json")
            ->post(
                $this->option('url'),
                $data);

        $this->warn("Response: " . $response->body());
    }
}
