<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MissionsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
         
        DB::table('missions')->insert(array (
            0 => 
            array (
                'brain_points' => '2000',
                'created_at' => '2022-01-24 17:47:46',
                'heart_points' => '1000',
                'id' => 12,
                'mission_name' => 'mission 1',
                'mission_type' => 'brain',
                'updated_at' => '2022-01-24 17:47:46',
            ),
            1 => 
            array (
                'brain_points' => '2000',
                'created_at' => '2022-01-24 18:19:47',
                'heart_points' => '1000',
                'id' => 13,
                'mission_name' => 'mission 2',
                'mission_type' => 'brain',
                'updated_at' => '2022-01-24 18:19:47',
            ),
            2 => 
            array (
                'brain_points' => '2000',
                'created_at' => '2022-01-24 18:20:20',
                'heart_points' => '1000',
                'id' => 14,
                'mission_name' => 'mission 3',
                'mission_type' => 'brain',
                'updated_at' => '2022-01-24 18:20:20',
            ),
        ));
        
        
    }
}