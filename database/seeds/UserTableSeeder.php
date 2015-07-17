<?php

use Illuminate\Database\Seeder;

use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'role' => 'admin',
            'email' => 'admin@google.com',
            'password' => bcrypt('123123'),
            'username' => 'admin'
        ]);
    }
}
