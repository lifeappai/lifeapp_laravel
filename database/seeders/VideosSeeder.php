<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideosSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('videos')->insert(array (
            0 => 
            array (
                'id' => 1,
                'topic_id' => 1,
                'title' => 'video 1',
                'video_url' => NULL,
                'duration' => '420',
                'after_duration' => '60',
                'video_type' => 'brain',
                'brain_points' => '2000',
                'heart_points' => '1000',
                'created_at' => '2022-01-20 23:59:33',
                'updated_at' => '2022-01-20 23:59:33',
            ),
        ));
        
        
    }
}