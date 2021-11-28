<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Other extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    // customers
    DB::table('orders_status')->insert([
      ['name' => 'сформирован'],
      ['name' => 'оплачен']
    ]);
  }
}
