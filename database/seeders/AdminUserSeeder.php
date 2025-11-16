<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@gotanzania.test');
        $adminPassword = env('ADMIN_PASSWORD', 'change-this-password');

        $roles = collect([
            'admin',
            'content-editor',
        ])->map(fn (string $role) => Role::firstOrCreate([
            'name' => $role,
            'guard_name' => 'web',
        ]));

        $adminRole = $roles->firstWhere('name', 'admin');

        $user = User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'remember_token' => Str::random(60),
            ],
        );

        if (! $user->hasRole($adminRole)) {
            $user->syncRoles([$adminRole]);
        }
    }
}
