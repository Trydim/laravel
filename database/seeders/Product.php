<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    //codes
    $codes = [
      [
        'symbol_code' => 'type1',
        'name'        => 'тип1'
      ],
      [
        'symbol_code' => 'type2',
        'name'        => 'тип2'
      ],
      [
        'symbol_code' => 'material1',
        'name'        => 'материал1'
      ],
      [
        'symbol_code' => 'material2',
        'name'        => 'материал2'
      ],
    ];
    DB::table('codes')->insert($codes);

    // section
    DB::table('sections')->insert([
      [
        'parent_id' => 0,
        'code'      => 'category1',
        'name'      => 'Раздел1',
      ],
      [
        'parent_id' => 0,
        'code'      => 'category2',
        'name'      => 'Раздел2',
      ],
      [
        'parent_id' => 1,
        'code'      => 'subCategory1',
        'name'      => 'ПодРаздел1',
      ],
    ]);

    // Money
    DB::table('money')->insert([
      [
        'code'       => 'USD',
        'name'       => 'Union State Dollars',
        'short_name' => '$',
      ],
      [
        'code'       => 'EUR',
        'name'       => 'Euro',
        'short_name' => '$',
      ],
      [
        'code'       => 'BYN',
        'name'       => 'Белорусский рубль',
        'short_name' => 'руб.',
      ],
    ]);
    DB::table('money')->insert([
      'code'       => 'RUS',
      'name'       => 'Российский рубль',
      'short_name' => 'руб.',
      'main'       => 1,
    ]);

    // units
    DB::table('units')->insert([
      'name'       => 'Штуки',
      'short_name' => 'шт.',
    ]);

    // elements
    for ($sectionId = 1; $sectionId < 4; $sectionId++) {
      for ($i = 0; $i < 10; $i++) {
        DB::table('elements')->insert([
          'element_type_code' => $codes[rand(0, 3)]['symbol_code'],
          'section_parent_id' => $sectionId,
          'name'              => 'элемент-' . Str::random(5),
        ]);
      }
    }

    // options_elements
    $elementId = 1;
    for ($sectionId = 1; $sectionId < 4; $sectionId++) {
      for ($i = 0; $i < 10; $i++) {
        DB::table('options_elements')->insert([
          'element_id' => $elementId,
          'name'       => 'вариант-' . Str::random(5),
        ]);
        $elementId++;
      }
    }
  }
}
