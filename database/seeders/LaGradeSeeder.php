<?php

namespace Database\Seeders;

use App\Models\LaGrade;
use Illuminate\Database\Seeder;

class LaGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            LaGrade::firstOrCreate([
                "name" => $i,
            ]);
        }
    }
}
