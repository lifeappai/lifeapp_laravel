<?php

namespace App\Console\Commands\Scripts;

use App\Models\LaQuestion;
use App\Models\LaQuestionOption;
use Illuminate\Console\Command;

class FixOptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:options';

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

        if (($open = fopen(storage_path('csv/1693974840.csv'), "r")) !== false) {
            $columns = fgetcsv($open, 1000, ",");

            $fromId = 252;

            while (($data = fgetcsv($open, 1000, ",")) !== false) {

                if (trim($data[0]) === '') {
                    continue;
                }

                $item = [];
                foreach ($columns as $key => $column) {
                    $item[strtolower(trim($column))] = trim($data[$key]);
                }

                $question = LaQuestion::find($fromId);

                $question->options()->delete();

/*                $i = 1;
                while (isset($item["option{$i}"]) && $item["option{$i}"] !== '') {
                    $option = [
                        "en" => $item["option{$i}"]
                    ];

                    $optionArry = [
                        'question_id' => $question->id,
                        'title' => $option,
                    ];
                    $option =  LaQuestionOption::create($optionArry);

                    if ($item['answer'] === "option{$i}") {
                        $question->answer_option_id = $option->id;
                        $question->save();
                    }
                    $i++;
                }
*/
                $fromId++;
            }

            fclose($open);
        }
        return 0;
    }
}
