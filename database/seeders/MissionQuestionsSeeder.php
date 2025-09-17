<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MissionQuestionsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('mission_questions')->insert(array (
            0 => 
            array (
                'created_at' => '2022-01-24 17:47:46',
                'id' => 15,
                'mission_id' => 12,
                'question_image' => 'public/missions/61eee642b1e05_Screenshot from 2021-12-17 22-21-17.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 17:47:46',
            ),
            1 => 
            array (
                'created_at' => '2022-01-24 17:47:46',
                'id' => 16,
                'mission_id' => 12,
                'question_image' => 'public/missions/61eee642b32f3_Screenshot from 2021-12-17 21-22-36.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 17:47:46',
            ),
            2 => 
            array (
                'created_at' => '2022-01-24 18:19:47',
                'id' => 17,
                'mission_id' => 13,
                'question_image' => 'public/missions/61eeedc36b07a_Screenshot from 2021-12-17 22-21-17.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 18:19:47',
            ),
            3 => 
            array (
                'created_at' => '2022-01-24 18:19:47',
                'id' => 18,
                'mission_id' => 13,
                'question_image' => 'public/missions/61eeedc36c5e8_Screenshot from 2021-12-17 21-22-36.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 18:19:47',
            ),
            4 => 
            array (
                'created_at' => '2022-01-24 18:20:20',
                'id' => 19,
                'mission_id' => 14,
                'question_image' => 'public/missions/61eeede429905_Screenshot from 2021-12-17 22-21-17.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 18:20:20',
            ),
            5 => 
            array (
                'created_at' => '2022-01-24 18:20:20',
                'id' => 20,
                'mission_id' => 14,
                'question_image' => 'public/missions/61eeede42b04f_Screenshot from 2021-12-17 21-22-36.png',
                'question_type' => NULL,
                'updated_at' => '2022-01-24 18:20:20',
            ),
        ));
        
        
    }
}