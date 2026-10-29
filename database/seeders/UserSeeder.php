<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts. 'role' is not fillable, so it is assigned
     * explicitly with forceFill() instead of create() input.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
        ]);
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        User::create([
            'name' => 'Antonio Wisnu',
            'email' => 'staff1@gmail.com',
            'password' => Hash::make('staff'),
        ]);

        User::create([
            'name' => 'Dodo Wiyaja',
            'email' => 'staff2@gmail.com',
            'password' => Hash::make('staff'),
        ]);
    }
}
