<?php
namespace App\Models\Traits;

trait All {

  static function getMyAll() {
    return self::all();
  }
}
