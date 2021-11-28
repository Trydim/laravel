<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    // permission
    DB::table('permission')->insert([
      ['name' => 'admin'],
      ['name' => 'manager'],
    ]);

    // users
    DB::table('users')->insert([
      [
        'permission_id' => '1',
        'login'         => 'admin',
        'password'      => password_hash('123', PASSWORD_BCRYPT),
        'name'          => 'admin_name',
        'email'         => 'admin@mail.com',
        'phone'         => '+1 (234) 567-89-01',
        'created_at'    => date('Y-m-d h:i:s'),
        'updated_at'  => date('Y-m-d h:i:s'),
      ],
      [
        'permission_id' => '2',
        'login'         => 'user',
        'password'      => password_hash('123', PASSWORD_BCRYPT),
        'name'          => 'user_name',
        'email'         => 'user@mail.com',
        'phone'         => '+1 (234) 567-89-02',
        'created_at'    => date('Y-m-d h:i:s'),
        'updated_at'  => date('Y-m-d h:i:s'),
      ],
    ]);

    // customers
    DB::table('customers')->insert([
      'name'     => 'customer_name',
      'contacts' => '{"phone":"+7 (123) 456 78 90","email":"as@as.by","address":"address"}',
    ]);
  }
}
