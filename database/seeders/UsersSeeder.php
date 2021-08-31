<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $users = [];

    $users[] = [
      'name' => 'Tchi',
      'email' => 'tchi.devica@gmail.com',
      'password' => Hash::make('password'),
      'role' => 'super-admin',
      'created_at' => date('Y-m-d H:i:s'),
    ];

    foreach ($users as $user) {
      DB::table('users')->insert($user);
    }
  }
}
