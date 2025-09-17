<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('questions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'quiz_id' => 1,
                'question_title' => 'question 1',
                'question_type' => 'brain',
                'question_image' => NULL,
                'question_audio' => NULL,
                'answer' => '3',
                'created_at' => '2022-01-21 22:13:23',
                'updated_at' => '2022-01-21 22:13:23',
            ),
            1 => 
            array (
                'id' => 2,
                'quiz_id' => 1,
                'question_title' => 'question 2',
                'question_type' => 'heart',
                'question_image' => NULL,
                'question_audio' => NULL,
                'answer' => '2',
                'created_at' => '2022-01-21 22:13:23',
                'updated_at' => '2022-01-21 22:13:23',
            ),
        ));
        
        
    }
}