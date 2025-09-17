<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subjects')->insert([
            'name' => 'Science',
            'flag' => '1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
