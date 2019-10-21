<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$yhYEOJ7r74dDHemZ4DF4Z./uH8H1J/tNBifNBpkoNkq1Tb8mSZ8mK',
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
