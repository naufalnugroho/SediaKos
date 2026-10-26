<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! $email || ! $password) {
            throw new RuntimeException('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di .env lokal sebelum membuat admin.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name', 'Administrator'),
                'password' => Hash::make($password),
                'role' => 'admin',
            ],
        );
    }
}