<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('options')->insert(array (
            0 => 
            array (
                'id' => 1,
                'question_id' => 1,
                'option_title' => 'option 1',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:14:56',
                'updated_at' => '2022-01-21 23:14:56',
            ),
            1 => 
            array (
                'id' => 2,
                'question_id' => 1,
                'option_title' => 'option 2',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:14:56',
                'updated_at' => '2022-01-21 23:14:56',
            ),
            2 => 
            array (
                'id' => 3,
                'question_id' => 1,
                'option_title' => 'option 3',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:17',
                'updated_at' => '2022-01-21 23:15:17',
            ),
            3 => 
            array (
                'id' => 4,
                'question_id' => 1,
                'option_title' => 'option 4',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:17',
                'updated_at' => '2022-01-21 23:15:17',
            ),
            4 => 
            array (
                'id' => 5,
                'question_id' => 2,
                'option_title' => 'option 1',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:34',
                'updated_at' => '2022-01-21 23:15:34',
            ),
            5 => 
            array (
                'id' => 6,
                'question_id' => 2,
                'option_title' => 'option 2',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:34',
                'updated_at' => '2022-01-21 23:15:34',
            ),
            6 => 
            array (
                'id' => 7,
                'question_id' => 2,
                'option_title' => 'option 3',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:48',
                'updated_at' => '2022-01-21 23:15:48',
            ),
            7 => 
            array (
                'id' => 8,
                'question_id' => 2,
                'option_title' => 'option 3',
                'option_image' => NULL,
                'created_at' => '2022-01-21 23:15:48',
                'updated_at' => '2022-01-21 23:15:48',
            ),
        ));
        
        
    }
}