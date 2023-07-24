<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'    =>  'admin',
                'email'    =>  'admin',
                'password'   =>  Hash::make('admin'),
                'status'    =>  'active'
            ],
        ];



        foreach ($users as $user){
            if(! \App\Models\User::where('email', $user['email'])->exists()){
                \App\Models\User::create($user);
            }
        }
    }
}
