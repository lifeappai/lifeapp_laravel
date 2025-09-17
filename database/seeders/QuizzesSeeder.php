<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizzesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('quizzes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'video_id' => 1,
                'quiz_name' => 'Quiz 1',
                'no_of_question' => '5',
                'brain_points' => '2000',
                'heart_points' => '1000',
                'created_at' => '2022-01-21 22:10:52',
                'updated_at' => '2022-01-21 22:10:52',
            ),
        ));
        
        
    }
}