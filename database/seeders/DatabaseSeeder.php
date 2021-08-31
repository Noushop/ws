<?php

namespace Database\Seeders;

use Database\Seeders\UsersSeeder;
use Database\Seeders\CompaniesSeeder;
use Database\Seeders\CompanieUserSeeder;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      UsersSeeder::class,
    ]);
  }
}
