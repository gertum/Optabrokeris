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

    public const ADMIN2_EMAIL = 'admin2@inkodus.lt';
    public const ADMIN2_PASSWORD = 'Labas1234';

    public function run(): void
    {
        $this->checkAndCreateUser(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $this->checkAndCreateUser(self::ADMIN2_EMAIL, self::ADMIN2_PASSWORD);
   }

   private function checkAndCreateUser($email, $password) {
       /** @var Collection $users */
       $users = User::query()->where(['email'=>$email]);

       if ( $users->count() != 0) {
           echo sprintf("User [%s] already exists\n", $email);
           return;
       }

       User::query()->create([
           'email'=> $email,
           'name' => 'admin',
           'password' => password_hash($password, PASSWORD_BCRYPT),
           'email_verified_at' => now(),
           'remember_token' => Str::random(10),
       ]);
   }
}
