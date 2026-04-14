<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'       => 'Administrador',
            'email'      => 'admin@mutual.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'box_nombre' => 'Administración',
            'box_status' => 'libre',
        ]);
    }
}
