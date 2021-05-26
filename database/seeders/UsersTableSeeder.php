<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = "Sander";
        $user->email = "sander.plomp@live.nl";
        $user->password = bcrypt('Nellie1612');
        $user->is_admin = true;
        $user->save();
    }
}
