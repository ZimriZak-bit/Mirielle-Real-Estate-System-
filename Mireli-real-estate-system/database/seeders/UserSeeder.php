<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;


//  Demo accounts for everyone 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Mireli Admin', 'admin@mireli.test', '0700000001', User::ROLE_ADMIN],
            ['Mireli Manager', 'manager@mireli.test', '0700000002', User::ROLE_MANAGEMENT],
            ['Amina Client', 'client1@mireli.test', '0700000011', User::ROLE_CLIENT],
            ['Brian Client', 'client2@mireli.test', '0700000012', User::ROLE_CLIENT],
            ['Carol Client', 'client3@mireli.test', '0700000013', User::ROLE_CLIENT],
        ];

        foreach ($accounts as [$name, $email, $phone, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'phone' => $phone, 'role' => $role, 'password' => 'password'],
            );
        }
    }
}
