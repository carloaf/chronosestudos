<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $emails = config('chronos.admin_emails', []);

        foreach ($emails as $email) {
            $email = Str::lower($email);

            $existing = User::where('email', $email)->first();

            if ($existing) {
                $existing->forceFill([
                    'is_admin' => true,
                    'is_active' => true,
                ])->save();

                continue;
            }

            User::create([
                'name' => $email === 'admin@chronos.br' ? 'Admin Chronos' : 'Carlos Fernandes',
                'email' => $email,
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_active' => true,
            ]);
        }
    }
}
