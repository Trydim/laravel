<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
  use HasFactory;

  /**
   * Db tables
   *
   * @var string
   */
  protected $table = 'permission';

  /**
   * Следует ли обрабатывать временные метки модели.
   *
   * @var bool
   */
  public $timestamps = false;

  /**
   * Значения по умолчанию для атрибутов модели.
   *
   * @var array
   */
  protected $attributes = [
    'access_val' => '',
  ];

  /**
   * @param int $id
   * @return bool
   */
  static function currentUserIsAdmin(int $id = 0): bool {
    $name = self::find($id ? $id : auth()->user()->permission_id)->name;
    return preg_match('/(admin|админ)/i', $name);
  }

  /**
   * @return array
   */
  static function getAllPermission(): array {
    return array_map(function ($per) {
      $per['accessVal'] = json_decode($per['accessVal']);
      return $per;
    }, self::select('id', 'name', 'access_val AS accessVal')->get()->toArray());
  }
}
