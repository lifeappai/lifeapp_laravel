<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('topics')->insert(array (
            0 => 
            array (
                'id' => 1,
                'level_id' => 1,
                'title' => 'topic 1',
                'description' => 'topic',
                'image' => '',
                'created_at' => '2022-01-20 23:14:09',
                'updated_at' => '2022-01-20 23:14:09',
            ),
            1 => 
            array (
                'id' => 2,
                'level_id' => 1,
                'title' => 'topic 1',
                'description' => 'topic',
                'image' => '',
                'created_at' => '2022-01-20 23:14:11',
                'updated_at' => '2022-01-20 23:14:11',
            ),
        ));
        
        
    }
}