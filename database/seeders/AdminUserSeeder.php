<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin@inkodus.lt';
    public const ADMIN_PASSWORD = 'Labas123';
    public function run(): void
    {


        /** @var Collection $users */
        $users = User::query()->where(['email'=>self::ADMIN_EMAIL]);

        if ( $users->count() != 0) {
            echo "Admin user already exists\n";
            return;
        }

        User::query()->create([
            'email'=>self::ADMIN_EMAIL,
            'name' => 'admin',
            'password' => password_hash(self::ADMIN_PASSWORD, PASSWORD_BCRYPT),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
   }
}