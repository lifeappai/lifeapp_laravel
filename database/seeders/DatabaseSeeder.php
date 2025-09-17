<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // $this->call([
        //     SubjectSeeder::class,
        //     LevelsSeeder::class,
        //     TopicsSeeder::class,
        //     MissionsSeeder::class,
        //     MissionQuestionsSeeder::class,
        //     OptionsSeeder::class,
        //     QuestionsSeeder::class,
        //     QuizzesSeeder::class,
        //     VideosSeeder::class,
        // ]);
        // $this->call(MissionsTableSeeder::class);
        // $this->call(MissionQuestionsTableSeeder::class);
        // $this->call(LevelsTableSeeder::class);
        $this->call(LaGradeSeeder::class);
    }
}
