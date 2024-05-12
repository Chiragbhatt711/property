<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $checkAdminExist = User::where(['email'=>'admin@admin.com'])->first();

        if(!$checkAdminExist)
        {
            $user = User::create([
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin#2024'),
                'user_type' => 1
            ]);
        }
    }
}
