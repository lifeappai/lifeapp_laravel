<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminCheck = User::where('email', 'admin@gmail.com')->first();
        if (!$adminCheck) {
            $admin = new User();
            $admin->name = 'admin';
            $admin->email = 'admin@gmail.com';
            $admin->type = UserType::Admin;
            $admin->password = Hash::make('123456');
            $admin->save();
        }
    }
}
