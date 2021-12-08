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
        $user->first_name = "John";
        $user->last_name = "Doe";
        $user->email = "test@email.nl";
        $user->phone_number = "0400000000";
        $user->password = bcrypt('test123');
        $user->is_admin = true;
        $user->save();
    }
}
