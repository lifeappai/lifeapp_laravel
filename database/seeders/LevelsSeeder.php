<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('levels')->insert(array (
            0 => 
            array (
                'created_at' => '2022-01-25 00:24:59',
                'description' => NULL,
                'flag' => 1,
                'id' => 1,
                'img_name' => NULL,
                'level' => 1,
                'subject_id' => 1,
                'total_question' => NULL,
                'total_rewards' => NULL,
                'updated_at' => '2022-01-25 00:25:11',
            ),
            1 => 
            array (
                'created_at' => '2022-01-25 00:24:59',
                'description' => NULL,
                'flag' => 0,
                'id' => 2,
                'img_name' => NULL,
                'level' => 2,
                'subject_id' => 1,
                'total_question' => NULL,
                'total_rewards' => NULL,
                'updated_at' => '2022-01-25 00:25:11',
            ),
            2 => 
            array (
                'created_at' => '2022-01-25 00:24:59',
                'description' => NULL,
                'flag' => 0,
                'id' => 3,
                'img_name' => NULL,
                'level' => 3,
                'subject_id' => 1,
                'total_question' => NULL,
                'total_rewards' => NULL,
                'updated_at' => '2022-01-25 00:25:11',
            ),
            3 => 
            array (
                'created_at' => '2022-01-24 19:13:37',
                'description' => 'maths',
                'flag' => 0,
                'id' => 4,
                'img_name' => 'public/levels/61eefa61dbda4_Screenshot from 2022-01-24 23-42-44.png',
                'level' => 5,
                'subject_id' => 1,
                'total_question' => '4',
                'total_rewards' => '100',
                'updated_at' => '2022-01-24 19:13:37',
            ),
        ));
        
        
    }
}